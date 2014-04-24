<?

	return $this->DB->selectAssocs("SELECT `pl_kody_pocztowe_pna`.`id` as 'id', 
	`pl_kody_pocztowe_pna`.`nazwa` as 'nazwa', 
	`pl_kody_pocztowe_pna`.`ulica` as 'ulica', 
	`pl_kody_pocztowe_pna`.`numery` as 'numery', 
	`pl_kody_pocztowe_pna`.`miejscowosc` as 'miejscowosc', 
	`pl_miejscowosci`.`id` AS 'miejscowosc.id', 
	`pl_miejscowosci`.`NAZWA` AS 'miejscowosc.nazwa', 
	`pl_miejscowosci`.`parent_id` AS 'miejscowosc.parent_id', 
	`pl_miejscowosci`.`parent_nazwa` AS 'miejscowosc.parent_nazwa', 
	`pl_miejscowosci_rodzaje`.`NAZWA_RM` as 'miejscowosc.typ', 
	`pl_gminy`.`id` AS 'gmina.id',
	`pl_gminy`.`pl_powiat_id` AS 'gmina.powiat_id',
	`pl_gminy`.`nazwa` AS 'gmina.nazwa', 
	`pl_gminy_typy`.`nazwa` AS 'gmina.typ'
	FROM `pl_kody_pocztowe_pna` 
	JOIN `pl_miejscowosci` ON `pl_kody_pocztowe_pna`.`miejscowosc_id` = `pl_miejscowosci`.`id` 
	JOIN `pl_miejscowosci_rodzaje` ON `pl_miejscowosci`.`typ_id` = `pl_miejscowosci_rodzaje`.`id` 
	JOIN `pl_gminy` ON `pl_miejscowosci`.`gmina_id` = `pl_gminy`.`id` 
	JOIN `pl_gminy_typy` ON `pl_gminy`.`typ_id` = `pl_gminy_typy`.`id` 
	WHERE `pl_kody_pocztowe_pna`.`kod_id`='" . addslashes($id) . "' AND `pl_kody_pocztowe_pna`.`akcept`='1' 
	LIMIT 1000");