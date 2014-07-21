<?

	return array(
		'posel_id' => $this->DB->selectValue("SELECT id FROM s_poslowie_kadencje WHERE twitter_account_id='" . addslashes( $id ) . "'"),
	);