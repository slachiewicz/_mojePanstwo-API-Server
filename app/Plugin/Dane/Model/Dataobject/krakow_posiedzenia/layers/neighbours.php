<?
		
	$id = (int) $id;
	list($date) = $this->DB->selectRow("SELECT `date` FROM `pl_gminy_krakow_posiedzenia` WHERE `id`='$id'");
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT `id`, `date` as `title` FROM `pl_gminy_krakow_posiedzenia` WHERE `date`<'" . $date . "' ORDER BY `date` DESC"),
		'next' => $this->DB->selectAssoc("SELECT `id`, `date` as `title` FROM `pl_gminy_krakow_posiedzenia` WHERE `date`>'" . $date . "' ORDER BY `date` ASC"),
	);
	
	if( $data['previous'] )
		$data['previous']['title'] = 'Posiedzenie ' . strip_tags( dataSlownie( $data['previous']['title'] ) );
		
	if( $data['next'] )
		$data['next']['title'] = 'Posiedzenie ' . strip_tags( dataSlownie( $data['next']['title'] ) );
	
	return $data;