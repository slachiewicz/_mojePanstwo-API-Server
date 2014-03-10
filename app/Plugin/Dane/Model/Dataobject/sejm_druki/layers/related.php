<?
	
		
	$output = array(
		'groups' => array(),
	);

	$objects = array(
		'projekty' => array(),
		'punkty' => array(),
	);
	
	$druk_id = $id;
	
	
	
	
	
	foreach( $this->DB->selectValues("SELECT DISTINCT(s_projekty_druki.projekt_id) FROM s_projekty_druki JOIN s_projekty ON s_projekty_druki.projekt_id=s_projekty.id WHERE s_projekty.typ_id='1' AND s_projekty.podrzedny='0' AND s_projekty_druki.druk_id='" . $druk_id . "'") as $projekt_id )
		$objects['projekty'][] = array(
			'dataset' => 'legislacja_projekty_ustaw',
			'object_id' => $projekt_id,
		);
		
		
	foreach( $this->DB->selectValues("SELECT DISTINCT(punkt_id) FROM s_posiedzenia_punkty_druki WHERE druk_id='" . $druk_id . "'") as $punkt_id )
		$objects['punkty'][] = array(
			'dataset' => 'sejm_posiedzenia_punkty',
			'object_id' => $punkt_id,
		);
	
		
	
	
	
	
	if( !empty($objects['projekty']) )
		$output['groups'][] = array(
			'id' => 'projekty',
			'title' => 'Projekty, których dotyczy ten druk',
			'objects' => $objects['projekty'],
		);
	
	
	if( !empty($objects['punkty']) )
		$output['groups'][] = array(
			'id' => 'punkty',
			'title' => 'Rozpatrywania w Sejmie',
			'objects' => $objects['punkty'],
		);
	


	

	return $output;