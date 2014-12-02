<?
	
	return $this->DB->selectAssocs("SELECT id, adres, telefon, email, podstawowe FROM s_poslowie_biura WHERE posel_id='" . addslashes( $id ) . "' AND deleted='0' ORDER BY ord ASC");