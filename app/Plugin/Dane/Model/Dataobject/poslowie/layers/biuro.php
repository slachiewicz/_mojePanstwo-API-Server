<?
	
	return $this->DB->selectAssoc("SELECT id, adres, telefon, email, podstawowe FROM s_poslowie_biura WHERE posel_id='" . addslashes( $id ) . "' AND deleted='0' AND podstawowe='1' LIMIT 1");