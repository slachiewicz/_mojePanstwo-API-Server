<?

$data = $this->DB->query("SELECT `krs_prokurenci`.nazwa, `krs_prokurenci`.imiona, `krs_prokurenci`.rodzaj, `krs_osoby`.`id`, YEAR(CURRENT_TIMESTAMP) - YEAR(krs_osoby.data_urodzenia) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(krs_osoby.data_urodzenia, 5)) as `wiek`
		FROM `krs_prokurenci` 
		LEFT JOIN `krs_osoby` 
		ON `krs_prokurenci`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_prokurenci`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_prokurenci`.`deleted`='0'
		ORDER BY `krs_prokurenci`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_prokurenci']['nazwa'] . ' ' . $d['krs_prokurenci']['imiona']),
        'funkcja' => _ucfirst($d['krs_prokurenci']['rodzaj']),
        'wiek' => @$d[0]['wiek'],
        'osoba_id' => @$d['krs_osoby']['id'],
    );
}


return $output;