<?
	
	return $this->DB->selectAssocs("
		SELECT 
			`pl_gminy_radni`.`id`, 
			`pl_gminy_radni`.`imiona`, 
			`pl_gminy_radni`.`nazwisko`, 
			`pl_gminy_radni`.`avatar_id` 
		FROM 
			`pl_gminy_radni` 
			JOIN `pl_gminy_radni_krakow` ON 
				`pl_gminy_radni`.`id` = `pl_gminy_radni_krakow`.`id` AND 
				`pl_gminy_radni`.`kadencja_7` = '1' AND 
				`pl_gminy_radni`.`akcept` = '1' AND 
				`pl_gminy_radni`.`aktywny` = '1' AND 
				`pl_gminy_radni_krakow`.`avatar` = '1' 
		ORDER BY 
			`pl_gminy_radni`.`l_glosow` DESC 
	");