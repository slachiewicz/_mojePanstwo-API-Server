<?
	
	$id = (int) $id;
	
	list($nazwa, $gmina_id) = $this->DB->selectRow("SELECT nazwa, gmina_id FROM pl_gminy_radni WHERE id='$id'");
	
	return array(
		'previous' => $this->DB->selectAssoc("SELECT id, nazwa as `title` FROM pl_gminy_radni WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND nazwa<'" . $nazwa . "' ORDER BY `nazwa` DESC"),
		'next' => $this->DB->selectAssoc("SELECT id, nazwa as `title` FROM pl_gminy_radni WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND nazwa>'" . $nazwa . "' ORDER BY `nazwa` ASC"),
	);