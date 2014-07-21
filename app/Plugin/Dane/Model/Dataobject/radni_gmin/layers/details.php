<?

	$data = $this->DB->selectAssoc("SELECT `opis_html` FROM `pl_gminy_radni_krakow` WHERE `id`='" . addslashes( $id ) . "'");
	
	return $data;