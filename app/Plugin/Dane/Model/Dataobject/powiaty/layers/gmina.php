<?

	return $this->DB->selectValue("SELECT id FROM pl_gminy WHERE pl_powiat_id='" . addslashes( $id ) . "'");