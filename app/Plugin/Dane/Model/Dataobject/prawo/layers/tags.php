<?

	return $this->DB->selectAssocs("
		SELECT `ISAP_hasla`.`id`, `ISAP_hasla`.`q` 
		FROM `prawo-hasla` 
			JOIN `ISAP_hasla` 
				ON `prawo-hasla`.`haslo_id` = `ISAP_hasla`.`id` 
		WHERE `prawo-hasla`.`prawo_id`='" . addslashes( $id ) . "' AND `prawo-hasla`.`deleted` = '0' 
		ORDER BY `ISAP_hasla`.`q` ASC 
		LIMIT 100 
	");