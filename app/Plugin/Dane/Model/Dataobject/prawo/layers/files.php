<?

	$prawo = $this->DB->selectAssoc("
		SELECT `isap_id`, `rcl_id`, `src` FROM `prawo` WHERE `id`='" . addslashes( $id ) . "' LIMIT 1
	");
	
	$isap = false;	
	
	if( $prawo['isap_id'] )
		$isap = $this->DB->selectDictionary("
		SELECT `isap_typ_id`, `dokument_id` FROM `ISAP_pozycje_pliki` WHERE `pozycja_id` = '" . addslashes( $prawo['isap_id'] ) . "' LIMIT 20
		");
	
	
	
	
	
	
	$files = array();
	
	
	if( $isap && array_key_exists(3, $isap) ) {
	
		$files[] = array(
			'slug' => 'tekst_aktualny',
			'title' => 'Tekst aktualny',
			'desc' => 'Aktualna wersja ujednolicona',
			'dokument_id' => $isap[3],
		);
	
	}
	
	
	if( $isap && array_key_exists(2, $isap) ) {
		
		$files[] = array(
			'slug' => 'tekst',
			'title' => 'Tekst',
			'desc' => 'Wersja opublikowana w Dzienniku Ustaw',
			'dokument_id' => $isap[2],
		);
		
	} else {
		
		if( in_array($prawo['src'], array('DzU', 'MP')) && $prawo['rcl_id'] ) {
		
			$table = $prawo['src'] . '_pozycje';
			
			if( $dokument_id = $this->DB->selectValue("SELECT `dokument_id` FROM `$table` WHERE `id`='" . $prawo['rcl_id'] . "' LIMIT 1") )
				$files[] = array(
					'slug' => 'tekst',
					'title' => 'Tekst',
					'desc' => 'Wersja opublikowana w Dzienniku Ustaw',
					'dokument_id' => $dokument_id,
				);				
		
		}
		
	}
	
	
	
	return $files;
	
	
	
	
	return $rcl;