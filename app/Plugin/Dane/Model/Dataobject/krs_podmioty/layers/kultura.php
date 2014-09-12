<?

	$data = $this->DB->selectAssocs("
		SELECT `pkd2007`.`podklasa`, `pkd2007`.`nazwa`, `pkd2007`.`kultura_indeks_id` as 'indeks_id', `kultura_indeksy`.`nazwa` as 'indeks_nazwa' 
		FROM `krs_pozycje-pkd2007` 
		JOIN `pkd2007` 
			ON `krs_pozycje-pkd2007`.`pkd_id` = `pkd2007`.`id` 
		JOIN `kultura_indeksy` 
			ON `pkd2007`.`kultura_indeks_id` = `kultura_indeksy`.`id` 
		WHERE 
			`krs_pozycje-pkd2007`.`pozycja_id` = '" . addslashes( $id ) . "' AND 
			`pkd2007`.`kultura_indeks_id` IS NOT NULL AND 
			`pkd2007`.`kultura_indeks_id` != 0 AND 
			`pkd2007`.`kultura_indeks_id` != 30 
		ORDER BY
			`krs_pozycje-pkd2007`.`ord` ASC 
	");
	
	$data_count = count( $data );
	for( $i=0; $i<$data_count; $i++ ) {
		
		$data[ $i ]['score'] = $data_count - $i;
		
	}

	return array(
		'dzialalnosci' => $data,
	);

