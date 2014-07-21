<?
	
	$id = (int) $id;
	list($date, $gmina_id) = $this->DB->selectRow("SELECT `date`, `gmina_id` FROM `rady_posiedzenia` WHERE `id`='$id'");
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT `id`, `date` as `title` FROM `rady_posiedzenia` WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND `date`<'" . $date . "' ORDER BY `date` DESC"),
		'next' => $this->DB->selectAssoc("SELECT `id`, `date` as `title` FROM `rady_posiedzenia` WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND `date`>'" . $date . "' ORDER BY `date` ASC"),
	);
	
	if( $data['previous'] )
		$data['previous']['title'] = 'Posiedzenie ' . strip_tags( dataSlownie( $data['previous']['title'] ) );
		
	if( $data['next'] )
		$data['next']['title'] = 'Posiedzenie ' . strip_tags( dataSlownie( $data['next']['title'] ) );
	
	return $data;