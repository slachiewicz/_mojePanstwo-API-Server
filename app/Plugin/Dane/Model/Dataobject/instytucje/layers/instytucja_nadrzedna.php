<?
	
	return $this->DB->selectAssoc("
		SELECT 
			`administracja_publiczna`.`id`, 
			`administracja_publiczna`.`nazwa`, 
			`objects`.`slug` 
		FROM 
			`administracja_publiczna` JOIN `objects` ON
				`objects`.`dataset_id` = '150' AND 
				`administracja_publiczna`.`id` = `objects`.`object_id` 
			JOIN `administracja_publiczna` as `helper` ON 
				`administracja_publiczna`.`id` = `helper`.`parent_id` 
		WHERE 
			`helper`.`id` = '" . addslashes( $id ) . "' 
	");