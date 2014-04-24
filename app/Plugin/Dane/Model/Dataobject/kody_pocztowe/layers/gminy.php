<?

	return $this->DB->selectAssocs("SELECT `pl_gminy`.`id`, `pl_gminy`.`nazwa`, `pl_gminy`.`typ_id`, `pl_gminy_typy`.`nazwa` as 'typ_nazwa', `pl_gminy`.`pl_powiat_id` as 'powiat_id'
	FROM `pl_kody_pocztowe-gminy` 
	JOIN `pl_gminy` ON `pl_kody_pocztowe-gminy`.`gmina_id` = `pl_gminy`.`id` 
	JOIN `pl_gminy_typy` ON `pl_gminy`.`typ_id` = `pl_gminy_typy`.`id` 
	WHERE `pl_kody_pocztowe-gminy`.`kod_id`='" . addslashes($id) . "' AND `pl_kody_pocztowe-gminy`.`deleted`='0' 
	LIMIT 1000");