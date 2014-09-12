<?

	$glosowanie_id = $this->DB->selectValue("SELECT `glosowanie_id` FROM `pl_gminy_krakow_posiedzenia_punkty` WHERE `id`='". addslashes( $id ) ."'");
	
	if( $glosowanie_id ) {
		
		
		return $this->DB->selectAssocs("
			SELECT 
				`pl_gminy_krakow_glosowania_glosy`.`id`,
				`pl_gminy_krakow_glosowania_glosy`.`radny_str`,
				`pl_gminy_krakow_glosowania_glosy`.`glos_str`,
				`pl_gminy_krakow_glosowania_glosy`.`glos_id`,
				`pl_gminy_krakow_glosowania_glosy`.`radny_id`, 
				`pl_gminy_radni`.`nazwa`, 
				`pl_gminy_radni`.`komitet`, 
				`pl_gminy_radni`.`avatar_id` 
			FROM 
				`pl_gminy_krakow_glosowania_glosy` 
			JOIN
				`pl_gminy_radni` ON 
					`pl_gminy_krakow_glosowania_glosy`.`radny_id` = `pl_gminy_radni`.`id` 
			WHERE
				`pl_gminy_krakow_glosowania_glosy`.`glosowanie_id` = '" . $glosowanie_id . "' 
			ORDER BY 
				`pl_gminy_radni`.`nazwa` ASC 
			LIMIT 
				100
		");
		
		
	} else return array();