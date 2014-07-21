<?

	return array(
		'posel_id' => $this->DB->selectValue("SELECT id FROM s_poslowie_kadencje WHERE krs_osoba_id='" . addslashes( $id ) . "'"),
	);