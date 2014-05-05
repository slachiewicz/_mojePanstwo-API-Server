<?

	return $this->DB->selectAssocs("SELECT `ogloszenie_pozycja_numer` as 'numer', `data_publikacji` as 'data' FROM `uzp_dokumenty` WHERE `parent_id` = '$id' OR `id` = '$id' ORDER BY `file` ASC");