<?
	return $this->DB->selectAssoc("SELECT * FROM `pl_gminy_krakow_radni_oswiadczenia` WHERE `radny_id`='" . addslashes( $id ) . "'");