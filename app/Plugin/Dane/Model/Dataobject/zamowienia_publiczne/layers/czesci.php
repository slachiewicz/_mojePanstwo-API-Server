<?
	
	$zamowienie = $this->DB->selectAssoc("SELECT `zamowienie_id` as `id` FROM `uzp_dokumenty` WHERE `id`='$id' LIMIT 1");
	
	if( $zamowienie && $zamowienie['id'] ) {
		
		$q = "SELECT 
		`uzp_czesci`.`id`, 
		`uzp_czesci`.`nazwa`, 
		`uzp_czesci`.`numer`, 
		`uzp_czesci`.`data_zam`, 
		`uzp_czesci`.`liczba_ofert`, 
		`uzp_czesci`.`liczba_odrzuconych_ofert`, 
		`uzp_czesci`.`liczba_wykonawcow`, 
		`uzp_czesci`.`wartosc`, 
		`uzp_czesci`.`cena`, 
		`uzp_czesci`.`cena_min`, 
		`uzp_czesci`.`cena_max`, 
		`uzp_czesci`.`waluta`, 
		`uzp_czesci-wykonawcy`.`wykonawca_id` AS 'wykonawca.id', 
		`uzp_wykonawcy`.`nazwa_wyk` AS 'wykonawca.nazwa',  
		`uzp_wykonawcy`.`miejscowosc` AS 'wykonawca.miejscowosc' 
		FROM `uzp_czesci` 
		JOIN `uzp_czesci-wykonawcy` ON `uzp_czesci`.`id` = `uzp_czesci-wykonawcy`.`czesc_id` 
		JOIN `uzp_wykonawcy` ON `uzp_wykonawcy`.`id` = `uzp_czesci-wykonawcy`.`wykonawca_id` 
		WHERE `uzp_czesci`.`dokument_id` = '" . $zamowienie['id'] . "' AND `uzp_czesci`.`deleted` = '0' 
		ORDER BY `uzp_czesci`.`id` ASC, `uzp_czesci-wykonawcy`.`wykonawca_id` ASC 
		LIMIT 100
		";
		
		$temp = array();
		$data = $this->DB->selectAssocs($q);
		$czesci = array();
		
		if( !empty($data) ) {
		
			foreach( $data as $d )
				$temp[ $d['id'] ][] = $d;
				
			foreach( $temp as $czesc_id => $_czesci ) {
								
				$czesc = array(
					'id' => $czesc_id,
					'nazwa' => $_czesci[0]['nazwa'],
				    'numer' => $_czesci[0]['numer'],
				    'data_zam' => $_czesci[0]['data_zam'],
				    'liczba_ofert' => $_czesci[0]['liczba_ofert'],
				    'liczba_odrzuconych_ofert' => $_czesci[0]['liczba_odrzuconych_ofert'],
				    'liczba_wykonawcow' => $_czesci[0]['liczba_wykonawcow'],
				    'wartosc' => $_czesci[0]['wartosc'],
				    'cena' => $_czesci[0]['cena'],
				    'cena_min' => $_czesci[0]['cena_min'],
				    'cena_max' => $_czesci[0]['cena_max'],
				    'waluta' => $_czesci[0]['waluta'],
				);
				
				$wykonawcy = array();
				foreach( $_czesci as $_czesc )
					$wykonawcy[] = array(
						'id' => $_czesc['wykonawca.id'],
						'nazwa' => $_czesc['wykonawca.nazwa'],
						'miejscowosc' => $_czesc['wykonawca.miejscowosc'],
					);				
				$czesc['wykonawcy'] = $wykonawcy;
				
				$czesci[] = $czesc;
				
			}
		
		}
		
		return $czesci;
	
	} else return false;