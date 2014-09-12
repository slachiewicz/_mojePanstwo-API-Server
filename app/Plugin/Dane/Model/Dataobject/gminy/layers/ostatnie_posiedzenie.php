<?
	
	$posiedzenie = $this->DB->selectAssoc("SELECT `pl_gminy_krakow_posiedzenia`.`id`, `pl_gminy_krakow_posiedzenia`.`date`, `pl_gminy_krakow_posiedzenia`.`numer`, `pl_gminy_krakow_sesje`.`numer` as 'sesja_numer' FROM `pl_gminy_krakow_posiedzenia` JOIN `pl_gminy_krakow_sesje` ON `pl_gminy_krakow_posiedzenia`.`sesja_id` = `pl_gminy_krakow_sesje`.`id` ORDER BY `pl_gminy_krakow_posiedzenia`.`date` DESC LIMIT 1");
	
	if( $posiedzenie ) {
		
		
		App::import('model', 'MPCache');
	    $MPCache = new MPCache();
	    
	    $terms = $MPCache->getDataSource()->get('stats/krakow_posiedzenia/terms/' . $posiedzenie['id'] . '*/*');
		$terms = json_decode($terms, true);    
		
		
		return array(
			'data' => $posiedzenie,
			'terms' => $terms,
		);
	
	
	} else return false;