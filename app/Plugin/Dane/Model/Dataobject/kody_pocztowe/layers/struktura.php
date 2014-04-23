<?
	
	$q = "SELECT 
	`pl_kody_pocztowe_pna`.`id` as 'pna.id', 
	`pl_kody_pocztowe_pna`.`nazwa` as 'pna.nazwa', 
	`pl_kody_pocztowe_pna`.`ulica` as 'pna.ulica', 
	`pl_kody_pocztowe_pna`.`numery` as 'pna.numery', 
	`pl_kody_pocztowe_pna`.`miejscowosc` as 'pna.miejscowosc', 
	`pl_miejscowosci`.`id` AS 'miejscowosc.id', 
	`pl_miejscowosci`.`NAZWA` AS 'miejscowosc.nazwa', 
	`pl_miejscowosci`.`parent_id` AS 'miejscowosc.parent_id', 
	`pl_miejscowosci`.`parent_nazwa` AS 'miejscowosc.parent_nazwa', 
	`pl_miejscowosci_rodzaje`.`NAZWA_RM` as 'miejscowosc.typ', 
	`pl_gminy`.`id` AS 'gmina.id',
	`pl_gminy`.`nazwa` AS 'gmina.nazwa', 
	`pl_gminy_typy`.`nazwa` AS 'gmina.typ' 
	FROM `pl_kody_pocztowe_pna` 
	JOIN `pl_miejscowosci` ON `pl_kody_pocztowe_pna`.`miejscowosc_id` = `pl_miejscowosci`.`id`
	JOIN `pl_gminy` ON `pl_miejscowosci`.`gmina_id` = `pl_gminy`.`id` 
	JOIN `pl_gminy_typy` ON `pl_gminy`.`typ_id` = `pl_gminy_typy`.`id`	 
	LEFT JOIN `pl_miejscowosci_rodzaje` ON `pl_miejscowosci`.`typ_id` = `pl_miejscowosci_rodzaje`.`id`	 
	WHERE `pl_kody_pocztowe_pna`.`kod_id`='$id' AND `pl_kody_pocztowe_pna`.`akcept` = '1' AND
	`pl_kody_pocztowe_pna`.`urzad_pocztowy`='0'
	ORDER BY `pl_gminy_typy`.`id` ASC, `pl_gminy`.`nazwa` ASC, `pl_miejscowosci`.`parent_id` ASC, `pl_miejscowosci`.`NAZWA` ASC, `pl_kody_pocztowe_pna`.`nazwa` ASC, `pl_kody_pocztowe_pna`.`ulica` ASC, `pl_kody_pocztowe_pna`.`numery` ASC";
		
	$data = $this->DB->selectAssocs($q);
	$output = array();
	$_output = array();
	
	
	foreach( $data as $d )
		$_output[ $d['gmina.id'] ][] = $d;
	


	$temp_output = array();	
	foreach( $_output as $gmina_id => $miejscowosci )
	{
		$temp = array();
		foreach( $miejscowosci as $m ) {
			
			if( $m['miejscowosc.parent_id'] ) {
				
				$temp[ $m['miejscowosc.parent_id'] ]['czesci'][ $m['miejscowosc.id'] ][] = $m;
				
			} else {
				
				$temp[ $m['miejscowosc.id'] ]['miejsca'][] = $m;
				
			}
			
			
		
		}
		
		$temp_output[$gmina_id] = $temp;
	}
	$_output = $temp_output;
		
	
	if( !empty($_output) ) {
		foreach( $_output as $gmina_id => $miejscowosci ) {
			
			$gmina = array(
				'id' => $gmina_id,
				'nazwa' => false,
				'typ' => false,
				'miejscowosci' => array(),
			);
			
			
			if( !empty($miejscowosci) ) {
				foreach( $miejscowosci as $miejscowosc_id => $miejscowosc_data ) {
					
					$miejscowosc = array(
						'id' => $miejscowosc_id,
						'nazwa' => false,
						'typ' => false,
						'parent_id' => false,
						'parent_nazwa' => false,
						'miejsca' => array(),
						'czesci' => array(),
					);
					
					if( isset($miejscowosc_data['miejsca']) && !empty($miejscowosc_data['miejsca']) ) {
						foreach( $miejscowosc_data['miejsca'] as $miejsce ) {
						
							if( !$miejscowosc['nazwa'] )
								$miejscowosc['nazwa'] = $miejsce['miejscowosc.nazwa'];
								
							if( !$miejscowosc['typ'] )
								$miejscowosc['typ'] = $miejsce['miejscowosc.typ'];
								
							if( !$miejscowosc['parent_id'] )
								$miejscowosc['parent_id'] = $miejsce['miejscowosc.parent_id'];
								
							if( !$miejscowosc['parent_nazwa'] )
								$miejscowosc['parent_nazwa'] = $miejsce['miejscowosc.parent_nazwa'];
							
							if( !$gmina['nazwa'] )
								$gmina['nazwa'] = $miejsce['gmina.nazwa'];
								
							if( !$gmina['typ'] )
								$gmina['typ'] = $miejsce['gmina.typ'];
							
							$adres_parts = array();
							if( $miejsce['pna.nazwa'] )
								$adres_parts[] = $miejsce['pna.nazwa'];
							if( $miejsce['pna.ulica'] )
								$adres_parts[] = $miejsce['pna.ulica'];
							if( $miejsce['pna.numery'] )
								$adres_parts[] = $miejsce['pna.numery'];
							
							
							// if( !empty($adres_parts) )	
								$miejscowosc['miejsca'][] = array(
									'id' => $miejsce['pna.id'],
									'adres' => implode(', ', $adres_parts),
								);
							
						}
					
					}
					
					
					
					if( isset($miejscowosc_data['czesci']) && !empty($miejscowosc_data['czesci']) ) {
																	
						foreach( $miejscowosc_data['czesci'] as $czesc_id => $miejsca ) {
							
							$czesc = array(
								'id' => $czesc_id,
								'nazwa' => false,
								'typ' => false,
								'miejsca' => array(),
							);
							
							if( !empty($miejsca) ) {
								foreach( $miejsca as $miejsce ) {
									
									if( !$czesc['nazwa'] )
										$czesc['nazwa'] = $miejsce['miejscowosc.nazwa'];
										
									if( !$czesc['typ'] )
										$czesc['typ'] = $miejsce['miejscowosc.typ'];
									
									if( !$miejscowosc['nazwa'] )
										$miejscowosc['nazwa'] = $miejsce['miejscowosc.nazwa'];
										
									if( !$miejscowosc['typ'] )
										$miejscowosc['typ'] = $miejsce['miejscowosc.typ'];
										
									if( !$miejscowosc['parent_id'] )
										$miejscowosc['parent_id'] = $miejsce['miejscowosc.parent_id'];
										
									if( !$miejscowosc['parent_nazwa'] )
										$miejscowosc['parent_nazwa'] = $miejsce['miejscowosc.parent_nazwa'];
									
									if( !$gmina['nazwa'] )
										$gmina['nazwa'] = $miejsce['gmina.nazwa'];
										
									if( !$gmina['typ'] )
										$gmina['typ'] = $miejsce['gmina.typ'];
									
									$adres_parts = array();
									if( $miejsce['pna.nazwa'] )
										$adres_parts[] = $miejsce['pna.nazwa'];
									if( $miejsce['pna.ulica'] )
										$adres_parts[] = $miejsce['pna.ulica'];
									if( $miejsce['pna.numery'] )
										$adres_parts[] = $miejsce['pna.numery'];
									
									
									// if( !empty($adres_parts) )	
										$czesc['miejsca'][] = array(
											'id' => $miejsce['pna.id'],
											'adres' => implode(', ', $adres_parts),
										);
									
								}
							}
							
							$miejscowosc['czesci'][] = $czesc;
							
						}
					
					}
					
					$gmina['miejscowosci'][] = $miejscowosc;
					
				}
			
			}
			
			$output[] = $gmina;
			
		}
	
	}
	
	return $output;