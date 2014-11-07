<?
	
	return $this->DB->selectValue("SELECT p_txt FROM stenogramy_wystapienia WHERE id='" . addslashes( $id ) . "'");