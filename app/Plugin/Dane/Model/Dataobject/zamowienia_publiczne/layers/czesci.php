<?
	$numery = array();
	
	
	$zamowienie = $this->DB->selectAssoc("SELECT `id`, `typ_id`, `liczba_czesci`, `liczba_czesci_zamowien` FROM `uzp_dokumenty` WHERE `id`='$id' LIMIT 1");
	
	$udzielenie_liczba_czesci = (int) $zamowienie['liczba_czesci'];
	
	$zamowienie_czesci = array();
	$udzielenie_czesci = array();
	
	if( $zamowienie['liczba_czesci_zamowien'] ) {
		
		$q = "SELECT 
		`uzp_zamowienia_czesci`.`id`, 
		`uzp_zamowienia_czesci`.`nazwa`, 
		`uzp_zamowienia_czesci`.`opis`, 
		`uzp_zamowienia_czesci`.`numer`, 
		`uzp_zamowienia_czesci`.`slownik`, 
		`uzp_zamowienia_czesci`.`czas`, 
		`uzp_zamowienia_czesci`.`czas_mies`, 
		`uzp_zamowienia_czesci`.`data_rozpoczecia`, 
		`uzp_zamowienia_czesci`.`data_zakonczenia`, 
		`uzp_zamowienia_czesci`.`kryterium`,  
		`uzp_zamowienia_czesci_kryteria`.`nazwa` as 'kryterium_nazwa', 
		`uzp_zamowienia_czesci_kryteria`.`punkty` as 'kryterium_punkty' 
		FROM `uzp_zamowienia_czesci` 
		LEFT JOIN `uzp_zamowienia_czesci_kryteria` 
		ON `uzp_zamowienia_czesci`.`id` = `uzp_zamowienia_czesci_kryteria`.`czesc_id` 
		AND `uzp_zamowienia_czesci_kryteria`.`deleted`='0' 
		WHERE `uzp_zamowienia_czesci`.`dokument_id`='$id' AND `uzp_zamowienia_czesci`.`deleted`='0' 
		ORDER BY `numer` ASC";		
		$data = $this->DB->selectAssocs($q);
				
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
					'opis' => $_czesci[0]['opis'],
					'numer' => $_czesci[0]['numer'],
					'slownik' => $_czesci[0]['slownik'],
					'czas' => $_czesci[0]['czas'],
					'czas_mies' => $_czesci[0]['czas_mies'],
					'data_rozpoczecia' => $_czesci[0]['data_rozpoczecia'],
					'data_zakonczenia' => $_czesci[0]['data_zakonczenia'],
					'kryterium' => $_czesci[0]['kryterium'],
				);
				
				$kryteria = array();
				foreach( $_czesci as $_czesc )
					$kryteria[] = array(
						'nazwa' => $_czesc['kryterium_nazwa'],
						'punkty' => (int) $_czesc['kryterium_punkty'],
					);				
				$czesc['kryteria'] = $kryteria;
				
				$czesci[ $czesc['numer'] ] = $czesc;
				$numery[] = $czesc['numer'];
			}
		
		}
				
	}
	
	$zamowienie_czesci = $czesci;
	
	
	
	$udzielenia_ids = false;
	
	if( $zamowienie['typ_id']=='1' ) {
		
		$udzielenia_ids = $this->DB->selectValues("SELECT `id` FROM `uzp_dokumenty` WHERE `parent_id`='$id' AND `typ_id`='3' AND `liczba_czesci`>0");
				
	}
	
	
	
	
	
	
		
	if( $udzielenia_ids ) {
		
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
		WHERE `uzp_czesci`.`dokument_id` = '" . implode("' OR `uzp_czesci`.`dokument_id`='", $udzielenia_ids) . "' AND `uzp_czesci`.`deleted` = '0' 
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
				
				$czesci[ $czesc['numer'] ] = $czesc;
				
			}
		
		}
			
	}
	
	$udzielenie_czesci = $czesci;
	
	
	
	$output = array();
	
	foreach( $numery as $numer ) {
		
		$czesc = array();
		
		$zamowienie_czesc = $zamowienie_czesci[ $numer ];
		$udzielenie_czesc = $udzielenie_czesci[ $numer ];
		
		if( $zamowienie_czesc && $udzielenie_czesc )
			$czesc = array_merge($zamowienie_czesc, $udzielenie_czesc);
		elseif( $zamowienie_czesc )
			$czesc = $zamowienie_czesc;
		elseif( $udzielenie_czesc )
			$czesc = $udzielenie_czesc;
		
		if( !empty($czesc) ) 
			$output[] = $czesc;
		
	}
	
	return $output;
	