<?

	return $this->DB->selectAssocs("SELECT `pl_powiaty`.`id`, `pl_powiaty`.`nazwa`, `pl_powiaty`.`typ_id`, `pl_powiaty_typy`.`nazwa` as 'typ_nazwa' 
	FROM `pl_kody_pocztowe-powiaty` 
	JOIN `pl_powiaty` ON `pl_kody_pocztowe-powiaty`.`powiat_id` = `pl_powiaty`.`id` 
	JOIN `pl_powiaty_typy` ON `pl_powiaty`.`typ_id` = `pl_powiaty_typy`.`id` 
	WHERE `pl_kody_pocztowe-powiaty`.`kod_id`='" . addslashes($id) . "' AND `pl_kody_pocztowe-powiaty`.`deleted`='0' 
	LIMIT 1000");