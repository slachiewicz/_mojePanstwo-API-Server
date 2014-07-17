<?

	$data = $this->DB->selectAssocs("SELECT seria, liczba, rodzaj_uprzywilejowania
		FROM krs_emisje_akcji
		WHERE pozycja_id='" . addslashes($id) . "' AND `deleted`='0'
		ORDER BY `ord` ASC");

	
	foreach( $data as &$d ) {
		
		$ru = trim(str_replace('-', '', $d['rodzaj_uprzywilejowania']));
		if( !$ru )
			$d['rodzaj_uprzywilejowania'] = '';			
		
	}
	
	return $data;