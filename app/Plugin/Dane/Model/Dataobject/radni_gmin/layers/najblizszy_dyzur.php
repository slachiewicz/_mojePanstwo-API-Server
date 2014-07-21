<?

	$q = "
		SELECT `id`, `data`, `godz_str`, `adres`, `adres_wiecej`, `timestart`, `timestop` 
		FROM `pl_gminy_radni_dyzury` 
		WHERE `radny_id`='" . addslashes( $id ) . "' 
		AND `data`>=NOW() 
		ORDER BY `data` ASC, `ord` ASC 
		LIMIT 1
	";
		
	return $this->DB->selectAssoc($q);