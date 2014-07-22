<?
	
	$id = (int) $id;
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT id, nazwa as `title` FROM umowy WHERE id<'" . $id . "' ORDER BY `id` DESC"),
		'next' => $this->DB->selectAssoc("SELECT id, nazwa as `title` FROM umowy WHERE id>'" . $id . "' ORDER BY `id` ASC"),
	);
	
	return $data;