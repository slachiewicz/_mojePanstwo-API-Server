<?

	return $this->DB->selectAssoc("SELECT `twitter_sources`.`id`, `twitter_sources`.`name` 
		FROM `twitter_twitts` 
		JOIN `twitter_sources` ON `twitter_twitts`.`source_id` = `twitter_sources`.`id` 
		WHERE `twitter_twitts`.`id` = '" . addslashes( $id ) . "'
		LIMIT 1");