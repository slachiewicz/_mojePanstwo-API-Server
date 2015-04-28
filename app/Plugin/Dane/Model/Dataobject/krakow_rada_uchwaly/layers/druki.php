<?

	return $this->DB->selectValues("SELECT druk_id FROM prawo_lokalne_druki WHERE `uchwala_id`='" . addslashes( $id ) . "' AND `deleted`='0'");
