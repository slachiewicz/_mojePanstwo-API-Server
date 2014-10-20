<?

	return array(
		'opis_html' => $this->DB->selectValue("SELECT opis_html FROM administracja_publiczna WHERE id='" . addslashes( $id ) . "'"),
	);