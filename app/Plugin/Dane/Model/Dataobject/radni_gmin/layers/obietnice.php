<?
	
	return $this->DB->selectAssocs("SELECT text, zrodlo_url, zrzut_url, znaleziono, do_sprawdzenia FROM pl_gminy_radni_krakow_obietnice WHERE radny_id='" . addslashes( $id ) . "'");