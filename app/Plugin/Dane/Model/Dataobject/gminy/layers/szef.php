<?

	return $this->DB->selectAssoc("SELECT `pl_gminy_szefowie_`.`id`, `pl_gminy_szefowie_`.`nazwa`, `pl_gminy_szefowie_`.`wybranie_data`, `pl_gminy_szefowie_`.`komitet`, `pl_gminy`.`szef_stanowisko` as 'stanowisko' 
	FROM `pl_gminy_szefowie_` 
	JOIN `pl_gminy` 
		ON `pl_gminy_szefowie_`.`gmina_id` = `pl_gminy`.`id` 
	WHERE `pl_gminy_szefowie_`.`gmina_id`='" . addslashes( $id ) . "' 
	ORDER BY `pl_gminy_szefowie_`.`ord` ASC 
	LIMIT 1");