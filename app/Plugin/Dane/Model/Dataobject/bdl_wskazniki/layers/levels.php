<?
	
	return $this->DB->selectAssocs("SELECT `BDL_ntss`.`tab_id` as 'id', `BDL_ntss`.`tab_name` as 'label' FROM `BDL_podgrupy-ntss` JOIN `BDL_ntss` ON `BDL_podgrupy-ntss`.`nts_id` = `BDL_ntss`.`id` WHERE `BDL_podgrupy-ntss`.`podgrupa_id`='" . $id . "' AND `BDL_podgrupy-ntss`.`deleted`='0' AND `BDL_ntss`.`tab_id`!='' GROUP BY `BDL_ntss`.`tab_id` ORDER BY `BDL_ntss`.`id`");
