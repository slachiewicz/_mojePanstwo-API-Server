<?
	
	$output = array();
	$mowcy_ids = array();
	
	$q = "SELECT `wypowiedzi_funkcje`.`id` as 'funkcja_id', `wypowiedzi_funkcje`.`nazwa` as 'funkcja_nazwa', `mowcy`.`id` as 'mowca_id', `mowcy`.`nazwa` as 'mowca_nazwa', `mowcy`.`avatar` 
	FROM `s_druki` 
	JOIN `s_druki_projekty_ustaw` ON `s_druki`.`id` = `s_druki_projekty_ustaw`.`id` 
	LEFT JOIN `wypowiedzi_funkcje` ON `s_druki_projekty_ustaw`.`reprezentant_funkcja_id` = `wypowiedzi_funkcje`.`id` 
	LEFT JOIN `mowcy_poslowie` ON `s_druki_projekty_ustaw`.`reprezentant_id` = `mowcy_poslowie`.`posel_id` 
	LEFT JOIN `mowcy` ON `mowcy_poslowie`.`mowca_id` = `mowcy`.`id`
	WHERE `s_druki`.`projekt_id` = '$id'";
	
	$items = $this->DB->selectAssocs( $q );
	if( !empty( $items ) ) {
		foreach( $items as $item ) {
			
			$o = array_merge($item, array(
				'rola' => 'Przedstawiciel wnioskodawcy w Sejmie',
			));
			
			if( $mowca_id = $item['mowca_id'] ) {
				
				if( !in_array($mowca_id, $mowcy_ids) ) {
					$output[] = $o;
					$mowcy_ids[] = $mowca_id;
				}
				
			} else {
				$output[] = $o;
			}
			
		}
	}	
			
			
	$q = "SELECT `mowcy`.`id` as 'mowca_id', `mowcy`.`nazwa` as 'mowca_nazwa', `mowcy`.`avatar` 
	FROM `s_projekty_druki` 
	JOIN `s_druki_sprawozdania_komisji` ON `s_projekty_druki`.`druk_id` = `s_druki_sprawozdania_komisji`.`id` 
	LEFT JOIN `mowcy_poslowie` ON `s_druki_sprawozdania_komisji`.`sprawozdawca_id` = `mowcy_poslowie`.`posel_id` 
	LEFT JOIN `mowcy` ON `mowcy_poslowie`.`mowca_id` = `mowcy`.`id`
	WHERE `s_projekty_druki`.`projekt_id` = '$id'";
	
	$items = $this->DB->selectAssocs( $q );
	if( !empty( $items ) ) {
		foreach( $items as $item ) {
			
			$o = array_merge($item, array(
				'rola' => 'Sprawozdawca komisji',
				'funkcja_id' => 38,
				'funkcja_nazwa' => 'Poseł',
			));
			
			if( $mowca_id = $item['mowca_id'] ) {
				
				if( !in_array($mowca_id, $mowcy_ids) ) {
					$output[] = $o;
					$mowcy_ids[] = $mowca_id;
				}
				
			} else {
				$output[] = $o;
			}
			
		}
	}
			
	
	
	$q = "SELECT `mowcy`.`id` as 'mowca_id', `mowcy`.`nazwa` as 'mowca_nazwa', `mowcy`.`avatar`  
	FROM `s_projekty_druki` 
	JOIN `s_druki_dodatkowe_sprawozdania_komisji` ON `s_projekty_druki`.`druk_id` = `s_druki_dodatkowe_sprawozdania_komisji`.`id` 
	LEFT JOIN `mowcy_poslowie` ON `s_druki_dodatkowe_sprawozdania_komisji`.`sprawozdawca_id` = `mowcy_poslowie`.`posel_id` 
	LEFT JOIN `mowcy` ON `mowcy_poslowie`.`mowca_id` = `mowcy`.`id`
	WHERE `s_projekty_druki`.`projekt_id` = '$id'";
	
	$items = $this->DB->selectAssocs( $q );
	if( !empty( $items ) ) {
		foreach( $items as $item ) {
			
			$o = array_merge($item, array(
				'rola' => 'Sprawozdawca komisji',
				'funkcja_id' => 38,
				'funkcja_nazwa' => 'Poseł',
			));
			
			if( $mowca_id = $item['mowca_id'] ) {
				
				if( !in_array($mowca_id, $mowcy_ids) ) {
					$output[] = $o;
					$mowcy_ids[] = $mowca_id;
				}
				
			} else {
				$output[] = $o;
			}
			
		}
	}
	
	return $output;