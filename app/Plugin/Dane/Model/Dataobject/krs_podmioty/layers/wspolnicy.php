<?

$data = $this->DB->query("SELECT `krs_wspolnicy`.nazwa, `krs_wspolnicy`.imiona, `krs_wspolnicy`.udzialy_str, `krs_osoby`.`id`, `krs_osoby`.`data_urodzenia`, `krs_osoby`.`privacy_level` 
		FROM `krs_wspolnicy` 
		LEFT JOIN `krs_osoby` 
		ON `krs_wspolnicy`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_wspolnicy`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_wspolnicy`.`deleted`='0'
		ORDER BY `krs_wspolnicy`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_wspolnicy']['nazwa'] . ' ' . $d['krs_wspolnicy']['imiona']),
        'data_urodzenia' => $d['krs_osoby']['data_urodzenia'],
        'privacy_level' => $d['krs_osoby']['privacy_level'],
        'osoba_id' => @$d['krs_osoby']['id'],
        'funkcja' => @$d['krs_wspolnicy']['udzialy_str'],
    );
}


return $output;