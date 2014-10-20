<?

	$data = $this->DB->selectAssocs("
		SELECT
			`rady_komisje`.`id`,
			`rady_komisje`.`nazwa`,
			`rady_komisje_sklad`.`stanowisko_id` 
		FROM
			`rady_komisje_sklad` JOIN `rady_komisje` 
				ON `rady_komisje_sklad`.`komisja_id` = `rady_komisje`.`id`
		WHERE
			`rady_komisje_sklad`.`radny_id` = '" . addslashes( $id ) . "' 
		ORDER BY
			`rady_komisje_sklad`.`stanowisko_id` DESC,
			`rady_komisje`.`nazwa` ASC 
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