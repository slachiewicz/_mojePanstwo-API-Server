<?

	return $this->DB->selectAssocs("SELECT `pl_miejscowosci`.`id`, `pl_miejscowosci`.`nazwa`, `pl_miejscowosci`.`gmina_id`, `pl_gminy`.`nazwa` as 'gmina_nazwa', `pl_gminy`.`typ_id` as 'gmina_typ_id', `pl_gminy`.`pl_powiat_id` as 'powiat_id', `pl_miejscowosci`.`typ_id`, `pl_miejscowosci_rodzaje`.`NAZWA_RM` as 'typ_nazwa' 
	FROM `pl_kody_pocztowe-miejscowosci` 
	JOIN `pl_miejscowosci` ON `pl_kody_pocztowe-miejscowosci`.`miejscowosc_id` = `pl_miejscowosci`.`id` 
	JOIN `pl_miejscowosci_rodzaje` ON `pl_miejscowosci`.`typ_id` = `pl_miejscowosci_rodzaje`.`id` 
	JOIN `pl_gminy` ON `pl_miejscowosci`.`gmina_id` = `pl_gminy`.`id` 
	WHERE `pl_kody_pocztowe-miejscowosci`.`kod_id`='" . addslashes($id) . "' AND `pl_kody_pocztowe-miejscowosci`.`deleted`='0' 
	LIMIT 1000");