<?

	return $this->DB->selectAssocs("
		SELECT 
			`pl_gminy_radni_kandydaci`.`id`, 
			`pl_gminy_radni_kandydaci`.`komitet_id`, 
			`pl_gminy_radni_kandydaci`.`lista`, 
			`pl_gminy_radni_kandydaci`.`pozycja`, 
			`pl_gminy_radni_kandydaci`.`nazwa_rev` as 'nazwa', 
			`pl_gminy_radni_kandydaci`.`wybrany`, 
			`pl_gminy_radni_kandydaci`.`bez_wyborow`, 
			`pl_gminy_radni_kandydaci`.`plec`, 
			`pl_gminy_radni_kandydaci`.`rok_urodzenia`, 
			`pl_gminy_radni_kandydaci`.`l_glosow`, 
			`pl_gminy_radni_kandydaci`.`p_glosow`, 
			`pkw_komitety`.`skrot_nazwy` as 'komitet_nazwa',
			`pl_gminy_radni`.`id` as 'radny_id' 
		FROM 
			`pl_gminy_radni_kandydaci` 
		LEFT JOIN 
			`pl_gminy_radni` ON 
				`pl_gminy_radni_kandydaci`.`id` = `pl_gminy_radni`.`kandydat_id` 
		JOIN 
			`pkw_komitety` ON
				`pl_gminy_radni_kandydaci`.`komitet_id` = `pkw_komitety`.`id` 
		WHERE 
			`pl_gminy_radni_kandydaci`.`okreg_id` = '" . addslashes( $id ) . "' 
		ORDER BY `pl_gminy_radni_kandydaci`.`l_glosow` DESC");