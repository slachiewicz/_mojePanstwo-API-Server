<?

	$data = $this->DB->query("SELECT `glosy`.`glos_id`, `glosy`.`glos_str`, `poslowie`.`id`, `poslowie`.`nazwa`, `kluby`.`id`, `kluby`.`nazwa`, `kluby`.`skrot`
	FROM `s_glosy` as `glosy` 
	JOIN `s_poslowie_kadencje` as `poslowie` ON `glosy`.`posel_id` = `poslowie`.`id` 
	JOIN `s_kluby` as `kluby` ON `glosy`.`klub_id` = `kluby`.`id` 
	WHERE `glosy`.`glosowanie_id` = '" . $id . "' 
	ORDER BY `poslowie`.`nazwa_rev` ASC");
	
	return $data;