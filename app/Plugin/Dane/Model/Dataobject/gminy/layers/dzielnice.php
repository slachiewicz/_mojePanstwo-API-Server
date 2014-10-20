<?

	return $this->DB->selectAssocs("SELECT id, nazwa FROM pl_dzielnice WHERE pl_gminy_id='" . addslashes( $id ) . "' ORDER BY nazwa ASC");
	