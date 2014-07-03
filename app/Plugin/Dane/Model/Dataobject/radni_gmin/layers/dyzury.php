<?

	$q = "
		SELECT `id`, `data`, `godz_str`, `adres`, `adres_wiecej`, `timestart`, `timestop` 
		FROM `pl_gminy_radni_dyzury` 
		WHERE `radny_id`='" . addslashes( $id ) . "' ";
		
	$q_future = $q . "
		AND `data`>=NOW() 
		ORDER BY `data` ASC, `ord` ASC
	";
	
	$q_past = $q . "
		AND `data`<NOW() 
		ORDER BY `data` DESC, `ord` DESC
	";		
	
	return array(
		'future' => $this->DB->selectAssocs( $q_future ),
		'past' => $this->DB->selectAssocs( $q_past ),
	);