<?

$data = $this->DB->query("SELECT zmiana_tekst as 'nazwa'
		FROM krs_umowy as `table`  
		WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
		ORDER BY `ord` ASC");

$output = array();
foreach ($data as $d)
    $output[] = array(
        'nazwa' => $d['table']['nazwa'],
    );


return $output;