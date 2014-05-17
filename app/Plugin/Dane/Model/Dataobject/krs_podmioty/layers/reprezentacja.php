<?

$data = $this->DB->query("SELECT `krs_reprezentanci`.nazwa, `krs_reprezentanci`.imiona, `krs_reprezentanci`.funkcja, `krs_osoby`.`id`, YEAR(CURRENT_TIMESTAMP) - YEAR(krs_osoby.data_urodzenia) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(krs_osoby.data_urodzenia, 5)) as `wiek`, `krs_osoby`.`privacy_level` as 'privacy' 
		FROM `krs_reprezentanci` 
		LEFT JOIN `krs_osoby` 
		ON `krs_reprezentanci`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_reprezentanci`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_reprezentanci`.`deleted`='0'
		ORDER BY `krs_reprezentanci`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_reprezentanci']['nazwa'] . ' ' . $d['krs_reprezentanci']['imiona']),
        'funkcja' => _ucfirst($d['krs_reprezentanci']['funkcja']),
        'wiek' => @$d[0]['wiek'],
        'osoba_id' => @$d['krs_osoby']['id'],
    );
}


return $output;