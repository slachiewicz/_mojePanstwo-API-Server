<?
	
	return 'http://www.bip.krakow.pl/?sub_dok_id=' . $this->DB->selectValue("SELECT src_id FROM pl_gminy_radni_krakow WHERE id='" . addslashes( $id ) . "'");