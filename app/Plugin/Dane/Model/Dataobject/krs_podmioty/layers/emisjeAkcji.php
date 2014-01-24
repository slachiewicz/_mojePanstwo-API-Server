<?

$data = $this->DB->query("SELECT seria, liczba, rodzaj_uprzywilejowania
		FROM krs_emisje_akcji as `table` 
		WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
		ORDER BY `ord` ASC");

$output = array();
foreach ($data as $d)
    $output[] = $d['table'];

return $output;