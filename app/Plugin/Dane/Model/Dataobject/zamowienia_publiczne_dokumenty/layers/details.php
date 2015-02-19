<?php
	
	$output = array();
	
	$load_dat = false;
	
	
	
	if( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='1' ) {
		
		$output = $this->DB->selectAssoc("SELECT `typ_id`, `data_start`, `data_stop`, `oferty_data_stop`, `oferty_godz`, `czas_miesiace`, `czas_dni`, `oferty_liczba_dni`, `le_adres_aukcja`, `le_adres_opis`, `le_data_skl`, `le_godz_skl`, `le_term_otw`, `le_term_war_zam` FROM `uzp_dokumenty` WHERE `id`='$id' AND akcept='1'");
		
		// $output['kryteria'] = $this->DB->selectAssocs("SELECT `nazwa`, `punkty` FROM `uzp_dokumenty_kryteria` WHERE `dokument_id`='" . $id . "' AND `deleted`='0' ORDER BY `ord` ASC");
		
		$load_dat = true;
		
	} elseif( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='2' ) {
		
		
		$load_dat = true;
		
		
		

	} elseif( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='3' ) {

		
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
		WHERE `uzp_czesci`.`dokument_id` = '" . addslashes( $id ) . "' AND `uzp_czesci`.`deleted` = '0' 
		ORDER BY `uzp_czesci`.`id` ASC, `uzp_czesci-wykonawcy`.`wykonawca_id` ASC 
		LIMIT 1000
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
				$numery[] = $czesc['numer'];
				
			}
		
		}
		
		$output['czesci-wykonawcy'] = $czesci;
		
		$load_dat = true;
		

	}
	
	$output['data'] = $this->DB->selectAssoc("SELECT `ogloszenie_pozycja_numer` as 'numer', `data_publikacji` as 'data' FROM `uzp_dokumenty` WHERE `id`='$id' AND akcept='1'");
	
	if( $load_dat ) {
		
		$body = $this->S3Files->getBody('resources/UZP-details/' . $id . '.dat');
		
		if( $body && ($data = unserialize($body)) && is_array($data) ) {
			
			foreach( $data as $key => $value )
				if( in_array(str_ireplace('.', '', $value), array('Brak warunku szczegółowego', 'Zamawiający nie stawia szczególnych wymagań do tego warunku', 'nie określa się', 'Zamawiający nie wyznacza szczegółowego warunku w tym zakresie', 'Zamawiający nie stawia specjalnych wymagań odnośnie spełnienia tego warunku', 'Nie dotyczy', 'nie dotyczy', 'Zamawiający nie określa szczegółowo tego warunku', 'zamawiający nie precyzuje warunku w tym zakresie')) )
					unset( $data[ $key ] );
			
			unset( $data['niepelnosprawne'] );
			$output = array_merge($output, $data);
			
		}
		
	}
	
	if( 
		( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='1' ) || 
		( 
			( $this->data['data']['zamowienia_publiczne_dokumenty.tryb_id']=='3' ) &&
			( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='3' ) 
		) ||
		( 
			( $this->data['data']['zamowienia_publiczne_dokumenty.tryb_id']=='2' ) &&
			( $this->data['data']['zamowienia_publiczne_dokumenty.typ_id']=='3' ) 
		) 
	) {
	} else {
		
		unset( $output['przedmiot'] );
		
	}
	
	return $output;