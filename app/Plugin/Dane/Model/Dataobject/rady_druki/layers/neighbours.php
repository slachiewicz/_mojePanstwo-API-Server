<?
	
	$id = (int) $id;
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT id, tytul as `title` FROM rady_druki WHERE id<'" . $id . "' ORDER BY `id` DESC"),
		'next' => $this->DB->selectAssoc("SELECT id, tytul as `title` FROM rady_druki WHERE id>'" . $id . "' ORDER BY `id` ASC"),
	);

	return $data;