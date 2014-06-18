<?

	$data = $this->DB->selectAssocs("SELECT `s_projekty`.`id`,  `s_projekty`.`tytul`, `s_projekty`.`opis`, `s_projekty`.`autorzy_html`, `s_posiedzenia_punkty_druki`.`wynik_id`, `s_posiedzenia_punkty_druki`.`punkt_id`
	FROM `s_posiedzenia_punkty`  
	JOIN `s_posiedzenia_punkty_druki` ON `s_posiedzenia_punkty`.`id` = `s_posiedzenia_punkty_druki`.`punkt_id` 
	JOIN `s_projekty_druki` ON `s_posiedzenia_punkty_druki`.`druk_id` = `s_projekty_druki`.`druk_id` 
	JOIN `s_projekty` ON `s_projekty_druki`.`projekt_id` = `s_projekty`.`id` 
	WHERE `s_posiedzenia_punkty`.`posiedzenie_id` = '" . addslashes( $id ) . "' AND `s_posiedzenia_punkty_druki`.`wynik_id` != 0 AND `s_projekty`.`akcept`='1'
	GROUP BY `s_projekty`.`id`");
	
	$grupy = array(
		'przyjete' => array(),
		'odrzucone' => array(),
		'dalsze_prace' => array(),
	);
	
	
	if( !empty($data) )
		foreach( $data as $d ) 
			if( $d['wynik_id']=='1' ) {
				
				$grupy['przyjete'][] = $d;
				
			} elseif( $d['wynik_id']=='2' ) {
				
				$grupy['odrzucone'][] = $d;
				
			} else {
				
				$grupy['dalsze_prace'][] = $d;
				
			}
	
	
	return $grupy;