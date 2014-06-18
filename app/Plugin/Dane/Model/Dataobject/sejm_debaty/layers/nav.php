<?
	
	$data = $this->DB->selectAssocs("SELECT 
	`s_posiedzenia`.`id` as 'posiedzenie.id', 
	`s_posiedzenia`.`tytul_str` as 'posiedzenie.tytul', 
	`s_posiedzenia`.`data_tytul` as 'posiedzenie.opis', 
	
	`s_posiedzenia_punkty`.`id` as 'punkt.id', 
	`s_posiedzenia_punkty`.`nr_int` as 'punkt.numer', 
	`s_posiedzenia_punkty`.`tytul` as 'punkt.tytul', 
	`s_posiedzenia_punkty`.`decyzja_opis` as 'punkt.wynik', 
	
	`stenogramy_subpunkty`.`id` as 'debata.id', 
	`stenogramy_subpunkty`.`tytul` as 'debata.tytul', 
	`stenogramy_subpunkty`.`tytul_i` as 'debata.numer', 
	`stenogramy_subpunkty`.`stats_str` as 'debata.opis', 
	`stenogramy_subpunkty`.`ilosc_wystapien` as 'debata.liczba_wystapien' 
	
	FROM `stenogramy_subpunkty-punkty` 
	JOIN `stenogramy_subpunkty-punkty` as `stenogramy_subpunkty-punkty_copy` ON `stenogramy_subpunkty-punkty_copy`.`punkt_id` = `stenogramy_subpunkty-punkty`.`punkt_id` 
	JOIN `stenogramy_subpunkty` ON `stenogramy_subpunkty-punkty`.`subpunkt_id` = `stenogramy_subpunkty`.`id`
	JOIN `s_posiedzenia_punkty` ON `stenogramy_subpunkty-punkty`.`punkt_id` = `s_posiedzenia_punkty`.`id`
	JOIN `s_posiedzenia` ON `s_posiedzenia_punkty`.`posiedzenie_id` = `s_posiedzenia`.`id`
	WHERE `stenogramy_subpunkty-punkty_copy`.`subpunkt_id`='" . addslashes( $id ) . "' 
	ORDER BY `s_posiedzenia_punkty`.`nr_int` ASC, `stenogramy_subpunkty`.`i` ASC 
	LIMIT 100");
	
	
	$posiedzenie = array(
		'id' => $data[0]['posiedzenie.id'],
		'tytul' => $data[0]['posiedzenie.tytul'],
		'opis' => $data[0]['posiedzenie.opis'],
		'punkty' => array(),
	);
	
	$punkty = array();
	
	foreach( $data as $d ) {
		
		$punkty[ $d['punkt.id'] ]['id'] = $d['punkt.id'];
		$punkty[ $d['punkt.id'] ]['numer'] = $d['punkt.numer'];
		$punkty[ $d['punkt.id'] ]['tytul'] = $d['punkt.tytul'];
		$punkty[ $d['punkt.id'] ]['wynik'] = $d['punkt.wynik'];
		$punkty[ $d['punkt.id'] ]['debaty'][] = array(
			'id' => $d['debata.id'],
			'tytul' => $d['debata.tytul'],
			'numer' => $d['debata.numer'],
			'opis' => $d['debata.opis'],
			'liczba_wystapien' => $d['debata.liczba_wystapien'],
		);
		
	}
	
	foreach( $punkty as $punkt_id => $punkt )
		$posiedzenie['punkty'][] = $punkt;
	
	
	return array(
		'posiedzenie' => $posiedzenie
	);
	