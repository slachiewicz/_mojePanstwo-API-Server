<?

$data = $this->DB->selectAssocs("SELECT `krs_wspolnicy`.`id`, `krs_wspolnicy`.nazwa, `krs_wspolnicy`.imiona, `krs_wspolnicy`.udzialy_str, `krs_osoby`.`id` as 'osoba_id', `krs_pozycje`.`id` as 'krs_id', `krs_osoby`.`data_urodzenia`, `krs_osoby`.`privacy_level`, `krs_wspolnicy`.`udzialy_status`, `krs_wspolnicy`.`udzialy_liczba`, `krs_wspolnicy`.`udzialy_wartosc_jedn`, `krs_wspolnicy`.`udzialy_wartosc` 
		FROM `krs_wspolnicy` 
		LEFT JOIN `krs_osoby` ON `krs_wspolnicy`.`osoba_id` = `krs_osoby`.`id` 
		LEFT JOIN `krs_pozycje` ON `krs_wspolnicy`.`krs_id` = `krs_pozycje`.`id` 
		WHERE `krs_wspolnicy`.`pozycja_id` = '" . addslashes($id) . "' AND `krs_wspolnicy`.`deleted`='0'
		ORDER BY `krs_wspolnicy`.`ord` ASC LIMIT 100");

$output = array();
foreach ($data as $d) {
	
	$nazwa = $d['nazwa'];
	$imiona = $d['imiona'];
	
	if( !trim(str_replace('*', '', $nazwa)) )
		$nazwa = '';
		
	if( !trim(str_replace('*', '', $imiona)) )
		$imiona = '';
	
	
    $o = array(
        'nazwa' => _ucfirst(trim( $nazwa . ' ' . $imiona )),
        'data_urodzenia' => $d['data_urodzenia'],
        'privacy_level' => $d['privacy_level'],
        'osoba_id' => @$d['osoba_id'],
        'krs_id' => @$d['krs_id'],
        'id' => @$d['id'],
        'funkcja' => @$d['udzialy_str'],
    );
    
    if( $d['udzialy_status']=='2' ) {
	    
	    $o = array_merge($o, array(
	        'udzialy_liczba' => @$d['udzialy_liczba'],
	        'udzialy_wartosc_jedn' => @$d['udzialy_wartosc_jedn'],
	        'udzialy_wartosc' => @$d['udzialy_wartosc'],
	    ));
	    
    }
    
    $output[] = $o;
}


return $output;