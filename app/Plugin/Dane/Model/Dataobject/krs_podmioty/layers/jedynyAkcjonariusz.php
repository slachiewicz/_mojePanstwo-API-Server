<?

$data = $this->DB->query("SELECT `krs_jedyni_akcjonariusze`.nazwa, `krs_jedyni_akcjonariusze`.imiona, `krs_osoby`.`id`, YEAR(CURRENT_TIMESTAMP) - YEAR(krs_osoby.data_urodzenia) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(krs_osoby.data_urodzenia, 5)) as `wiek`
		FROM `krs_jedyni_akcjonariusze` 
		LEFT JOIN `krs_osoby` 
		ON `krs_jedyni_akcjonariusze`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_jedyni_akcjonariusze`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_jedyni_akcjonariusze`.`deleted`='0'
		ORDER BY `krs_jedyni_akcjonariusze`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_jedyni_akcjonariusze']['nazwa'] . ' ' . $d['krs_jedyni_akcjonariusze']['imiona']),
        'wiek' => @$d[0]['wiek'],
        'osoba_id' => @$d['krs_osoby']['id'],
    );
}


return $output;