<?

	return $this->DB->selectValue("SELECT id FROM prawo_ustawy_glowne WHERE prawo_id='" . addslashes( $id ) . "'");