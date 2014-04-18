<?
	
	$output = array();
	
	if( $rcl_projekt_id = $this->DB->selectValue("SELECT id FROM rcl_projekty WHERE projekt_id='$id'") ) {
		
		
		$items = $this->DB->selectAssocs("SELECT `rcl_dokumenty`.`id`, `rcl_dokumenty`.`tytul` 
		FROM `rcl_dokumenty` 
		JOIN `rcl_katalogi` ON `rcl_dokumenty`.`katalog_id` = `rcl_katalogi`.`id` 
		WHERE `rcl_katalogi`.`projekt_id` = '$rcl_projekt_id' 
		AND `rcl_katalogi`.`tytul` LIKE 'pisma z uwagami' 
		ORDER BY `rcl_katalogi`.`id` DESC, `rcl_dokumenty`.`id` DESC 
		LIMIT 100");
				
		
	}
	
	return $output;