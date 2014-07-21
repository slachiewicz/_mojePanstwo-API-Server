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

function pl_wiek( $data )
{
	$birthDate = explode("-", substr($data, 0, 10));
    $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y") - $birthDate[0]) - 1) : (date("Y") - $birthDate[0]));
    return $age;
}

function number_format_h($n, $decimals = 0, $dec_point = '.', $thousands_sep = ' ')
{
    // first strip any formatting;
    $n = (0 + str_replace(",", "", $n));

    // is this a number?
    if (!is_numeric($n)) return false;

    // now filter it;
    if ($n > 1000000000000000) return round(($n / 1000000000000000), 1) . ' Bld';
    else if ($n > 1000000000000) return round(($n / 1000000000000), 1) . ' B';
    else if ($n > 1000000000) return round(($n / 1000000000), 1) . ' Mld';
    else if ($n > 1000000) return round(($n / 1000000), 1) . ' M';
    else if ($n > 1000) return round(($n / 1000), 1) . ' k';

    return number_format($n, $decimals, $dec_point, $thousands_sep);
}

function dataSlownie($data)
{
    $_data = $data;

    $timestamp = strtotime($data);
    if (!$timestamp)
        return false;

    $data = date('Y-m-d', $timestamp);

    if ($data == date('Y-m-d', time())) // TODAY
    {

        $str = 'dzisiaj';

    } else {


        $___vars = array(
            'miesiace' => array(
                'celownik' => array(
                    1 => 'stycznia',
                    2 => 'lutego',
                    3 => 'marca',
                    4 => 'kwietnia',
                    5 => 'maja',
                    6 => 'czerwca',
                    7 => 'lipca',
                    8 => 'sierpnia',
                    9 => 'września',
                    10 => 'października',
                    11 => 'listopada',
                    12 => 'grudnia',
                ),
            ),
        );

        $parts = explode('-', substr($data, 0, 10));
        if (count($parts) != 3) return $data;

        $dzien = (int)$parts[2];
        $miesiac = (int)$parts[1];
        $rok = (int)$parts[0];


        $str = $dzien . ' ' . $___vars['miesiace']['celownik'][$miesiac] . ' ' . $rok . ' r.';

    }

    /*
    $time_str = @substr($_data, 11, 5);
    if( $time_str )
        $str .= ' ' . $time_str;
    */


    return $str;

}

function istripslashes($input) {
	if( is_string($input) )
		return stripslashes( $input );
	else
		return $input;
}