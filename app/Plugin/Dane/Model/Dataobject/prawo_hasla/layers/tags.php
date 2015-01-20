<?
	
	return $this->DB->selectAssocs("
		SELECT `ISAP_hasla`.`id`, `ISAP_hasla`.`q` 
		FROM `ISAP_hasla_powiazania` 
		JOIN `ISAP_hasla` ON
			`ISAP_hasla_powiazania`.`b` = `ISAP_hasla`.`id`
		WHERE `ISAP_hasla_powiazania`.`a` = '" . addslashes( $id ) . "'
		ORDER BY `ISAP_hasla_powiazania`.`count`  DESC 
		LIMIT 10
	");