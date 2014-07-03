<?
	
	$id = (int) $id;
	list($ord, $gmina_id) = $this->DB->selectRow("SELECT `global_ord`, `gmina_id` FROM `rady_posiedzenia_debaty` WHERE `id`='$id'");
	
	$data = array(
		'previous' => $this->DB->selectAssoc("SELECT `id`, CONCAT(`nr_str`, '. ', `tytul`) as `title` FROM `rady_posiedzenia_debaty` WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND `global_ord`<'" . $ord . "' ORDER BY `global_ord` DESC"),
		'next' => $this->DB->selectAssoc("SELECT `id`, CONCAT(`nr_str`, '. ', `tytul`) as `title` FROM `rady_posiedzenia_debaty` WHERE `akcept`='1' AND `gmina_id`='$gmina_id' AND `global_ord`>'" . $ord . "' ORDER BY `global_ord` ASC"),
	);
	
	return $data;