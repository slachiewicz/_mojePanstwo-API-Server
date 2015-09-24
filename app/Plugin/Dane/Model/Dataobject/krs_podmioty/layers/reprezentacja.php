<?

$data = $this->DB->query("SELECT `krs_reprezentanci`.nazwa, `krs_reprezentanci`.imiona, `krs_reprezentanci`.funkcja, `krs_osoby`.`id`, `krs_osoby`.`data_urodzenia`, `krs_osoby`.`privacy_level`  
		FROM `krs_reprezentanci` 
		LEFT JOIN `krs_osoby` 
		ON `krs_reprezentanci`.`osoba_id` = `krs_osoby`.`id` 
		WHERE `krs_reprezentanci`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_reprezentanci`.`deleted`='0'
		ORDER BY `krs_reprezentanci`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {
    $osoba = array(
        'nazwa' => _ucfirst($d['krs_reprezentanci']['nazwa'] . ' ' . $d['krs_reprezentanci']['imiona']),
        'funkcja' => _ucfirst($d['krs_reprezentanci']['funkcja'])
    );

    if (isset($d['krs_osoby']['id'])) {
        $osoba['data_urodzenia'] = $d['krs_osoby']['data_urodzenia'];
        $osoba['privacy_level'] = $d['krs_osoby']['privacy_level'];
        $osoba['osoba_id'] = $d['krs_osoby']['id'];
        $osoba['krs_osoby.url'] = Dataobject::apiUrl('krs_osoby', $d['krs_osoby']['id']);
    }

    $output[] = $osoba;
}


return $output;