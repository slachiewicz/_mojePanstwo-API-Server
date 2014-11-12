<?

	$id = (int) $id;
	
	$q = "
		SELECT 
			`pl_gminy_krakow_urzednicy`.`id`, 
			`pl_gminy_krakow_urzednicy`.`nazwa`, 
			`pl_gminy_krakow_urzednicy`.`opis`, 
			`krs_osoby-pozycje`.`reprezentat`, 
			`krs_osoby-pozycje`.`reprezentat_funkcja`, 
			`krs_osoby-pozycje`.`wspolnik`, 
			`krs_osoby-pozycje`.`akcjonariusz`, 
			`krs_osoby-pozycje`.`prokurent`, 
			`krs_osoby-pozycje`.`nadzorca`, 
			`krs_osoby-pozycje`.`zalozyciel`, 
			`krs_pozycje`.`id` as `organizacja_id`, 
			`krs_pozycje`.`nazwa` as `organizacja_nazwa`, 
			`krs_pozycje`.`forma_prawna_str` as `organizacja_forma_prawna` 
		FROM `pl_gminy_krakow_urzednicy` 
		JOIN `krs_osoby-pozycje` 
			ON `pl_gminy_krakow_urzednicy`.`krs_osoba_id` = `krs_osoby-pozycje`.`osoba_id` 
		JOIN `pl_gminy_krakow_oswiadczenia` 
			ON `pl_gminy_krakow_oswiadczenia`.`urzednik_id` = `pl_gminy_krakow_urzednicy`.`id` 
		JOIN `krs_pozycje` 
			ON `krs_pozycje`.`id` = `krs_osoby-pozycje`.`pozycja_id` 
		WHERE 
			`pl_gminy_krakow_urzednicy`.`krs_osoba_id` != 0 AND 
			`pl_gminy_krakow_oswiadczenia`.`jednostka_id` != 17 
		GROUP BY 
			`pl_gminy_krakow_urzednicy`.`id`, 
			`krs_pozycje`.`id` 			
		ORDER BY 
			`pl_gminy_krakow_urzednicy`.`nazwa` ASC 
	";
	
	$data = array();
	$temp = $this->DB->selectAssocs($q);
	
	if( !empty($temp) ) {
	
		foreach( $temp as $d ) {
			
			$data[ $d['id'] ]['urzednik'] = array(
				'id' => $d['id'],
				'nazwa' => $d['nazwa'],
				'opis' => $d['opis'],
			);
			$data[ $d['id'] ]['organizacje'][] = array(
				'id' => $d['organizacja_id'],
				'nazwa' => $d['organizacja_nazwa'],
				'forma_prawna' => $d['organizacja_forma_prawna'],
				'relacja' => array(
					'reprezentat' => $d['reprezentat'],
					'reprezentat_funkcja' => $d['reprezentat_funkcja'],
					'wspolnik' => $d['wspolnik'],
					'akcjonariusz' => $d['akcjonariusz'],
					'prokurent' => $d['prokurent'],
					'nadzorca' => $d['nadzorca'],
					'zalozyciel' => $d['zalozyciel'],
				),
			);
			$data[ $d['id'] ]['organizacje_count'] = count( $data[ $d['id'] ]['organizacje'] );
			
		}	
			
		$data = array_values($data);
		usort($data, function($a, $b){
			return !( $a['organizacje_count'] > $b['organizacje_count'] );
		});
	
	}
	
	return $data;