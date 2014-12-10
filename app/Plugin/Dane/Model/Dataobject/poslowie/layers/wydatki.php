<?

	$data = $this->DB->selectAssocs("SELECT `s_poslowie_rozliczenia`.`rocznik`, `s_poslowie_rozliczenia`.`dokument_id`, `s_poslowie_rozliczenia_pola`.`tytul`, `s_poslowie_rozliczenia_pola`.`nr`, `s_poslowie_rozliczenia-pola`.`wartosc` FROM `s_poslowie_rozliczenia` 
JOIN `s_poslowie_rozliczenia-pola` ON `s_poslowie_rozliczenia`.`id` = `s_poslowie_rozliczenia-pola`.`rozliczenie_id` 
JOIN `s_poslowie_rozliczenia_pola` ON `s_poslowie_rozliczenia-pola`.`pole_id` = `s_poslowie_rozliczenia_pola`.`id` 
WHERE `s_poslowie_rozliczenia`.`posel_id` = '" . addslashes( $id ) . "' AND `s_poslowie_rozliczenia`.`dane_akcept` = '1' 
ORDER BY `s_poslowie_rozliczenia`.`rocznik` DESC, `s_poslowie_rozliczenia_pola`.`nr` ASC, `s_poslowie_rozliczenia`.`id` ASC");

	
	$pola = array();
	$roczniki = array();
	
	if( !empty($data) )
		foreach( $data as $d ) {
		
			$pola[ $d['nr'] ] = array(
				'tytul' => $d['tytul'],
				'numer' => $d['nr'],
			);

			$roczniki[ $d['rocznik'] ]['pola'][] = $d['wartosc'];
			$roczniki[ $d['rocznik'] ]['dokument_id'] = $d['dokument_id'];
			$roczniki[ $d['rocznik'] ]['rok'] = $d['rocznik'];
			
		}
	
	$output = array(
		'liczba_pol' => count( $pola ),
		'liczba_rocznikow' => count( $roczniki ),
		'punkty' => array(),
		'roczniki' => array(),
	);
	
	foreach( $pola as $nr => $punkt )
		$output['punkty'][] = $punkt;
	
	unset( $pola );
		
	foreach( $roczniki as $rok => $rocznik )
		$output['roczniki'][] = $rocznik;
	
	unset( $roczniki );
	
	return $output;