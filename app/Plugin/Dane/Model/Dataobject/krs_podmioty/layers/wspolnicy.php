<?

$data = $this->DB->query("SELECT `krs_wspolnicy`.nazwa, `krs_wspolnicy`.imiona, `krs_wspolnicy`.udzialy_str, `krs_osoby`.`id`, YEAR(CURRENT_TIMESTAMP) - YEAR(krs_osoby.data_urodzenia) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(krs_osoby.data_urodzenia, 5)) as `wiek`
		FROM `krs_wspolnicy` 
		LEFT JOIN `krs_osoby` 
		ON `krs_wspolnicy`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_wspolnicy`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_wspolnicy`.`deleted`='0'
		ORDER BY `krs_wspolnicy`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_wspolnicy']['nazwa'] . ' ' . $d['krs_wspolnicy']['imiona']),
        'wiek' => @$d[0]['wiek'],
        'osoba_id' => @$d['krs_osoby']['id'],
        'funkcja' => @$d['krs_wspolnicy']['udzialy_str'],
    );
}


return $output;