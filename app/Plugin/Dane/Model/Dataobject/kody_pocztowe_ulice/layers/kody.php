<?
	return $this->DB->selectAssocs("SELECT `kod_id`, `kod`, `ulica`, `numery` FROM `pl_kody_pocztowe_pna` WHERE `ulica_id`='$id'");