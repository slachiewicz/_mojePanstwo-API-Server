<?

function _ucfirst($str)
{

    $words = explode(' ', trim($str));
    foreach ($words as &$w) {

        $rest = strtolower(substr($w, 1));
        $rest = str_replace(array(
            'Ę', 'Ó', 'Ą', 'Ś', 'Ł', 'Ż', 'Ź', 'Ć', 'Ń',
        ), array(
            'ę', 'ó', 'ą', 'ś', 'ł', 'ż', 'ź', 'ć', 'ń',
        ), $rest);

        $w = strtoupper($w[0]) . $rest;

    }
    return implode(' ', $words);

}


if (!function_exists('array_column')) {
    function array_column($array, $column_key, $index_key = null)
    {
        $output = array();
        if (is_array($array) && !empty($array))
            foreach ($array as $record)
                if (array_key_exists($column_key, $record)) {
                    if ($index_key)
                        $output[$record[$index_key]] = $record[$column_key];
                    else
                        $output[] = $record[$column_key];
                }
        return $output;
    }
}


function solr_q($q = null)
{

    $q = (string)@$q;
    $q = trim(str_replace(array('!', '&', '|', ':', '^', '[', ']', '{', '}', '~', '\\'), ' ', $q));

    if ($q == '')
        $q = '*';

    $q = ($q == '*') ? '*:*' : $q;

    return $q;

}