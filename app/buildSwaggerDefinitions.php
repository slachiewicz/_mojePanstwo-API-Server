<?php

require_once("Vendor/functions.php");

$output_directory = 'webroot/schemas/dane/templates/';
$api_root = 'http://api-server.dev/';

$response = file_get_contents($api_root . 'dane/zbiory');
$response = json_decode($response, true);

$datasets = $response['Dataobject'];

// pobrać wszystkie strony wyników
while(isset($response['Links']['next'])) {
    $response = file_get_contents($response['Links']['next']);
    $response = json_decode($response, true);

    $datasets = array_merge($datasets, $response['Dataobject']);
}

$definitions = array();
$dataset_slugs = array();

foreach ($datasets as $dataset) {
    $data = $dataset['data'];
    $slug = $data['zbiory.slug'];
    $title = $data['zbiory.nazwa'];
    $description = $data['zbiory.opis'];

    if (!$description) {
        $def['description'] = 'TODO';
    }

    array_push($dataset_slugs, $slug);

    print("Processing " . $slug . "..\n");

    $out_schema = $output_directory . $slug . '.json';

    $properties = array();

    // get fields based on sample object
    $sample_object = file_get_contents($api_root . 'dane/' . $slug . '?limit=1');
    $sample_object = json_decode($sample_object, true);
    $sample_object = $sample_object['Dataobject'][0];

    $sample_object = file_get_contents($api_root . 'dane/' . $slug . '/' . $sample_object['id'] .'?layers=*');
    $sample_object = json_decode($sample_object, true);

    file_put_contents($output_directory . $slug . '_example.json', json_format($sample_object));

    foreach ($sample_object['data'] as $field => $v) {
        list($dataset, $fld) = preg_split('/\\./', $field);
        if ($dataset == null or $fld == null) {
            throw new Exception("Wrong field format " . $field);
        }

        $fdef = array(
            'type' => 'string',
            'description' => 'TODO'
        );

        // guess type, @see Vendor/functions.php
        $estype = mpapi_get_field_type($fld);
        switch ($estype) {
            case 'date':
                $fdef['type'] = 'string';
                $fdef['format'] = 'date-time';
                break;

            case 'bigint':
            case 'int':
                $fdef['type'] = 'integer';
                break;

            case 'float':
                $fdef['type'] = 'number';
                break;
        }

        $properties[$field] = $fdef;
    }

    $layers_properties = array();
    if (isset($sample_object['layers']) and $sample_object['layers']) {
        foreach ($sample_object['layers'] as $name => $layer_data) {
            if (!in_array($name, array("dataset", "channels", "page", "subscribers", "uczestnicy", "subscription"))) {
                $layers_properties[$name] = array("description" => "TODO");
            }
        }
    }

    // save definition
    $def = array(
        'title' => $title,
        'allOf' => array(
            array('$ref' => $api_root . 'schemas/dane/dataobject.json'),
            array(
                'properties' => array(
                    'data' => array(
                        'properties' => $properties,
                        'additionalProperties' => 'false'
//                        'required' => array()
                    ),
                    'layers' => array(
                        'properties' => $layers_properties
                    )
                ),
                'required' => array('data')
            )
        ),
        'additionalProperties' => false
    );

    $definitions[$slug] = $def;

    file_put_contents($out_schema, json_format($def));
}

sort($dataset_slugs);
print(json_format($dataset_slugs));

// original code: http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/
// adapted to allow native functionality in php version >= 5.4.0
/**
 * Format a flat JSON string to make it more human-readable
 *
 * @param string $json The original JSON string to process
 *        When the input is not a string it is assumed the input is RAW
 *        and should be converted to JSON first of all.
 * @return string Indented version of the original JSON string
 */
function json_format($json) {
    if (!is_string($json)) {
        if (phpversion() && phpversion() >= 5.4) {
            return json_encode($json, JSON_PRETTY_PRINT);
        }
        $json = json_encode($json);
    }
    $result      = '';
    $pos         = 0;               // indentation level
    $strLen      = strlen($json);
    $indentStr   = "  ";
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;
    for ($i = 0; $i < $strLen; $i++) {
        // Speedup: copy blocks of input which don't matter re string detection and formatting.
        $copyLen = strcspn($json, $outOfQuotes ? " \t\r\n\",:[{}]" : "\\\"", $i);
        if ($copyLen >= 1) {
            $copyStr = substr($json, $i, $copyLen);
            // Also reset the tracker for escapes: we won't be hitting any right now
            // and the next round is the first time an 'escape' character can be seen again at the input.
            $prevChar = '';
            $result .= $copyStr;
            $i += $copyLen - 1;      // correct for the for(;;) loop
            continue;
        }

        // Grab the next character in the string
        $char = substr($json, $i, 1);

        // Are we inside a quoted string encountering an escape sequence?
        if (!$outOfQuotes && $prevChar === '\\') {
            // Add the escaped character to the result string and ignore it for the string enter/exit detection:
            $result .= $char;
            $prevChar = '';
            continue;
        }
        // Are we entering/exiting a quoted string?
        if ($char === '"' && $prevChar !== '\\') {
            $outOfQuotes = !$outOfQuotes;
        }
        // If this character is the end of an element,
        // output a new line and indent the next line
        else if ($outOfQuotes && ($char === '}' || $char === ']')) {
            $result .= $newLine;
            $pos--;
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        // eat all non-essential whitespace in the input as we do our own here and it would only mess up our process
        else if ($outOfQuotes && false !== strpos(" \t\r\n", $char)) {
            continue;
        }
        // Add the character to the result string
        $result .= $char;
        // always add a space after a field colon:
        if ($outOfQuotes && $char === ':') {
            $result .= ' ';
        }
        // If the last character was the beginning of an element,
        // output a new line and indent the next line
        else if ($outOfQuotes && ($char === ',' || $char === '{' || $char === '[')) {
            $result .= $newLine;
            if ($char === '{' || $char === '[') {
                $pos++;
            }
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        $prevChar = $char;
    }
    return $result;
}