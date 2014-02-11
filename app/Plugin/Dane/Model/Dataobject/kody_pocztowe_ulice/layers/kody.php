<?
	return $this->DB->selectAssocs("SELECT `kod_id`, `kod`, `ulica`, `numery` FROM `_kody_pocztowe_pna` WHERE `ulica_id`='$id'");