<?

	return $this->DB->selectAssocs("
		SELECT `pkd2007`.`id`, `pkd2007`.`nazwa`, `pkd2007`.`color`, `krs_pozycje-pkd_dzialy`.`score` 
		FROM `krs_pozycje-pkd_dzialy` 
		JOIN `pkd2007` 
			ON `krs_pozycje-pkd_dzialy`.`dzial_id` = `pkd2007`.`id`
			AND `krs_pozycje-pkd_dzialy`.pozycja_id='" . addslashes($id) . "' 
			AND `krs_pozycje-pkd_dzialy`.`score` >= 0.01 
		ORDER BY `krs_pozycje-pkd_dzialy`.`score` DESC 
		LIMIT 10
		");

