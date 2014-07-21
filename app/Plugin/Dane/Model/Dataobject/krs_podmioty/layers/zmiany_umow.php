<?

	$data = $this->DB->selectAssocs("SELECT zmiana_tekst 
			FROM krs_umowy 
			WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
			ORDER BY `ord` DESC");
	
	return $data;