<?

$output = array(
    'biznes' => array(),
    'ngo' => array(),
);

$posel = $this->DB->selectAssoc("SELECT id, krs_osoba_id FROM s_poslowie_kadencje WHERE id='$id'");

if ($posel && $posel['krs_osoba_id']) {

    $osoba_id = $posel['krs_osoba_id'];


    // REPREZENTANCI

    $reprezentanci = $this->DB->query("SELECT
		`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`kapital_zakladowy`, `organizacja`.`cel_dzialania`, `organizacja`.`data_rejestracji`, 
		`rola`.`id`, `rola`.`funkcja` as `label`, `rola`.`deleted`, 
		`forma`.`typ_id`
		FROM `krs_reprezentanci` as `rola` 
		JOIN `krs_pozycje` as `organizacja` ON `rola`.`pozycja_id` = `organizacja`.`id`
		JOIN `krs_formy_prawne` as `forma` ON `organizacja`.`forma_prawna_id` = `forma`.`id`
		AND `rola`.`osoba_id` = '$osoba_id' 
		AND `organizacja`.`akcept` = '1'
		ORDER BY `organizacja`.`id` ASC
		LIMIT 100");
    foreach ($reprezentanci as $i) {

        if ($i['forma']['typ_id'] == '1')
            $output['biznes'][] = $i;
        else
            $output['ngo'][] = $i;

    }


    // NADZORCY

    $nadzorcy = $this->DB->query("SELECT
		`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`kapital_zakladowy`, `organizacja`.`cel_dzialania`, `organizacja`.`data_rejestracji`, 
		`rola`.`id`, `rola`.`deleted`, 
		`forma`.`typ_id`
		FROM `krs_nadzorcy` as `rola` 
		JOIN `krs_pozycje` as `organizacja` ON `rola`.`pozycja_id` = `organizacja`.`id`
		JOIN `krs_formy_prawne` as `forma` ON `organizacja`.`forma_prawna_id` = `forma`.`id`
		AND `rola`.`osoba_id` = '$osoba_id' 
		AND `organizacja`.`akcept` = '1'
		ORDER BY `organizacja`.`id` ASC
		LIMIT 100");
    foreach ($nadzorcy as $i) {

        $i['rola']['label'] = 'Członek organu nadzoru';
        if ($i['forma']['typ_id'] == '1')
            $output['biznes'][] = $i;
        else
            $output['ngo'][] = $i;

    }


    // WSPÓLNICY

    $wspolnicy = $this->DB->query("SELECT
		`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`kapital_zakladowy`, `organizacja`.`data_rejestracji`, 
		`rola`.`id`, `rola`.`udzialy_str`, `rola`.`deleted` 
		FROM `krs_wspolnicy` as `rola` 
		JOIN `krs_pozycje` as `organizacja`
		ON `rola`.`pozycja_id` = `organizacja`.`id`
		AND `rola`.`osoba_id` = '$osoba_id' 
		AND `organizacja`.`akcept` = '1'
		ORDER BY `organizacja`.`id` ASC
		LIMIT 100");
    foreach ($wspolnicy as $i) {

        $i['rola']['label'] = 'Wspólnik';
        $output['biznes'][] = $i;
    }


    // AKCJONARIUSZE

    $akcjonariusze = $this->DB->query("SELECT
		`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`kapital_zakladowy`, `organizacja`.`cel_dzialania`, `organizacja`.`data_rejestracji`, 
		`rola`.`id`, `rola`.`deleted`, 
		`forma`.`typ_id`
		FROM `krs_jedyni_akcjonariusze` as `rola` 
		JOIN `krs_pozycje` as `organizacja` ON `rola`.`pozycja_id` = `organizacja`.`id`
		JOIN `krs_formy_prawne` as `forma` ON `organizacja`.`forma_prawna_id` = `forma`.`id`
		AND `rola`.`osoba_id` = '$osoba_id' 
		AND `organizacja`.`akcept` = '1'
		ORDER BY `organizacja`.`id` ASC
		LIMIT 100");
    foreach ($akcjonariusze as $i) {

        $i['rola']['label'] = 'Jedyny akcjonariusz';
        if ($i['forma']['typ_id'] == '1')
            $output['biznes'][] = $i;
        else
            $output['ngo'][] = $i;

    }


    // ZAŁOŻYCIELE

    $zalozyciele = $this->DB->query("SELECT
		`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`kapital_zakladowy`, `organizacja`.`cel_dzialania`, `organizacja`.`data_rejestracji`, 
		`rola`.`id`, `rola`.`deleted`, 
		`forma`.`typ_id` 
		FROM `krs_komitety_zal` as `rola` 
		JOIN `krs_pozycje` as `organizacja` ON `rola`.`pozycja_id` = `organizacja`.`id`
		JOIN `krs_formy_prawne` as `forma` ON `organizacja`.`forma_prawna_id` = `forma`.`id`
		AND `rola`.`osoba_id` = '$osoba_id' 
		AND `organizacja`.`akcept` = '1'
		ORDER BY `organizacja`.`id` ASC
		LIMIT 100");
    foreach ($zalozyciele as $i) {

        $i['rola']['label'] = 'Założyciel';
        if ($i['forma']['typ_id'] == '1')
            $output['biznes'][] = $i;
        else
            $output['ngo'][] = $i;

    }


}

return $output;