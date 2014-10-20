<?

	$q = "
		SELECT 
			`pl_budzety_wydatki`.`id`, 
			`pl_budzety_wydatki`.`dzial_id`, 
			`pl_budzety_wydatki`.`rozdzial_id`,
			`pl_budzety_wydatki`.`type`,
			
			`pl_budzety_wydatki_dzialy`.`tresc` as 'dzial_nazwa',
			`pl_budzety_wydatki_rozdzialy`.`tresc` as 'rozdzial_nazwa',
			
			`pl_budzety_wydatki`.`plan`, 
			`pl_budzety_wydatki`.`dotacje_i_subwencje`, 
			`pl_budzety_wydatki`.`swiadczenia_na_rzecz_osob_fizycznych`, 
			`pl_budzety_wydatki`.`wydatki_biezace_jednostek_budzetowych`, 
			`pl_budzety_wydatki`.`wydatki_majatkowe`, 
			`pl_budzety_wydatki`.`wydatki_na_obsluge_dlugu`, 
			`pl_budzety_wydatki`.`srodki_wlasne_ue`, 
			`pl_budzety_wydatki`.`wspolfinansowanie_ue`
						 
		FROM `pl_budzety_wydatki` 
			LEFT JOIN `pl_budzety_wydatki_czesci` 
				ON `pl_budzety_wydatki`.`czesc_id` = `pl_budzety_wydatki_czesci`.`id`
			LEFT JOIN `pl_budzety_wydatki_dzialy` 
				ON `pl_budzety_wydatki`.`dzial_id` = `pl_budzety_wydatki_dzialy`.`id` 
			LEFT JOIN `pl_budzety_wydatki_rozdzialy` 
				ON `pl_budzety_wydatki`.`rozdzial_id` = `pl_budzety_wydatki_rozdzialy`.`id` 
		WHERE
			`pl_budzety_wydatki_czesci`.`instytucja_id` = '" . addslashes( $id ) . "'
		ORDER BY
			`pl_budzety_wydatki`.`plan` DESC
	";
	
	
	
	$dzialy = array();
	
	$data = $this->DB->selectAssocs($q);
	foreach( $data as $d ) {
		
		if( $d['type']=='dzial' ) {
			
			if( isset($dzialy[ $d['dzial_id'] ]['calc']) ) {
				
				$dzialy[ $d['dzial_id'] ]['calc']['plan'] += $d['plan'];
				$dzialy[ $d['dzial_id'] ]['calc']['dotacje_i_subwencje'] += $d['dotacje_i_subwencje'];
				$dzialy[ $d['dzial_id'] ]['calc']['swiadczenia_na_rzecz_osob_fizycznych'] += $d['swiadczenia_na_rzecz_osob_fizycznych'];
				$dzialy[ $d['dzial_id'] ]['calc']['wydatki_biezace_jednostek_budzetowych'] += $d['wydatki_biezace_jednostek_budzetowych'];
				$dzialy[ $d['dzial_id'] ]['calc']['wydatki_majatkowe'] += $d['wydatki_majatkowe'];
				$dzialy[ $d['dzial_id'] ]['calc']['wydatki_na_obsluge_dlugu'] += $d['wydatki_na_obsluge_dlugu'];
				$dzialy[ $d['dzial_id'] ]['calc']['srodki_wlasne_ue'] += $d['srodki_wlasne_ue'];
				$dzialy[ $d['dzial_id'] ]['calc']['wspolfinansowanie_ue'] += $d['wspolfinansowanie_ue'];
				
			} else {
				$dzialy[ $d['dzial_id'] ]['calc'] = $d;
			}
		
		} elseif( $d['type']=='rozdzial' ) {
			
			$dzialy[ $d['dzial_id'] ]['data']['id'] = $d['dzial_id'];
			$dzialy[ $d['dzial_id'] ]['data']['nazwa'] = $d['dzial_nazwa'];
			$dzialy[ $d['dzial_id'] ]['items'][] = $d;
			
		}
		
	}
	
	$dzialy = array_values( $dzialy );
		
	
	return array(
		'wydatki_dzialy' => $dzialy,
	);