<?
	
	if( $id == 903 ) {
		
		return $this->DB->selectAssocs("SELECT id, numer, nazwa FROM pl_gminy_krakow_dzielnice ORDER BY id ASC");
	
	} else return false;