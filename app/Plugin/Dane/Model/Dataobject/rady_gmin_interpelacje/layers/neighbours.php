<?
	
	$id = (int) $id;
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT id, temat as `title` FROM pl_gminy_radni_interpelacje WHERE id<'" . $id . "' ORDER BY `id` DESC"),
		'next' => $this->DB->selectAssoc("SELECT id, temat as `title` FROM pl_gminy_radni_interpelacje WHERE id>'" . $id . "' ORDER BY `id` ASC"),
	);
	
	if( $data['previous'] )
		$data['previous']['title'] = 'Interpelacja w sprawie ' . lcfirst( $data['previous']['title'] );
		
	if( $data['next'] )
		$data['next']['title'] = 'Interpelacja w sprawie ' . lcfirst( $data['next']['title'] );
	
	return $data;