<?

	$data = $this->DB->selectAssocs("
		SELECT
			`pl_gminy_radni`.`id`,
			`pl_gminy_radni`.`imie`,
			`pl_gminy_radni`.`nazwisko`,
			`pl_gminy_radni`.`nazwa`,
			`rady_komisje_sklad`.`stanowisko_id` 
		FROM
			`rady_komisje_sklad` JOIN `pl_gminy_radni` 
				ON `rady_komisje_sklad`.`radny_id` = `pl_gminy_radni`.`id`
		WHERE
			`rady_komisje_sklad`.`komisja_id` = '" . addslashes( $id ) . "' 
		ORDER BY
			`rady_komisje_sklad`.`stanowisko_id` DESC,
			`pl_gminy_radni`.`nazwisko` ASC, 
			`pl_gminy_radni`.`imie` ASC
		LIMIT 100
	");
	
	$_map = array(
		'3' => array('Przewodniczący', 'danger'),
		'2' => array('Wiceprzewodniczący', 'warning'),
		'1' => array('Członek', 'default'),
	);
	
	foreach( $data as &$d ) {
		$d['stanowisko'] = $_map[ $d['stanowisko_id'] ][0];
		$d['label'] = $_map[ $d['stanowisko_id'] ][1];
	}
	
	return $data;