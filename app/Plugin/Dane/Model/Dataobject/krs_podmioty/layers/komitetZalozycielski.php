<?

$data = $this->DB->query("SELECT `krs_komitety_zal`.nazwa, `krs_komitety_zal`.imiona, `krs_osoby`.`id`, YEAR(CURRENT_TIMESTAMP) - YEAR(krs_osoby.data_urodzenia) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(krs_osoby.data_urodzenia, 5)) as `wiek`
		FROM `krs_komitety_zal` 
		LEFT JOIN `krs_osoby` 
		ON `krs_komitety_zal`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_komitety_zal`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_komitety_zal`.`deleted`='0'
		ORDER BY `krs_komitety_zal`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {

    $output[] = array(
        'nazwa' => _ucfirst($d['krs_komitety_zal']['nazwa'] . ' ' . $d['krs_komitety_zal']['imiona']),
        'wiek' => @$d[0]['wiek'],
        'osoba_id' => @$d['krs_osoby']['id'],
    );
}


return $output;