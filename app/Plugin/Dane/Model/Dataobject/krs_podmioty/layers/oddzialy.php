<?

$data = $this->DB->query("SELECT nazwa, siedziba, adres
		FROM krs_oddzialy as `table`  
		WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
		ORDER BY `ord` DESC");

$output = array();
foreach ($data as $d)
    $output[] = array(
        'nazwa' => $d['table']['nazwa'],
        'siedziba' => $d['table']['siedziba'],
        'adres' => $d['table']['adres'],
    );


return $output;