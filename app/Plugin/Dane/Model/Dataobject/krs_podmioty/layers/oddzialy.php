<?

	$data = $this->DB->selectAssocs("SELECT nazwa, adres
			FROM krs_oddzialy  
			WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
			ORDER BY `ord` ASC 
			LIMIT 100");
	
	return $data;