<?php

class Wyjazdyposlow extends AppModel
{

    public $useTable = false;

    public function getStats()
    {
		
		App::import('model','DB');
		$DB = new DB();
		
		// App::Import('ConnectionManager');
		// $MPSearch = ConnectionManager::getDataSource('MPSearch');
		    
        
        $output = array();
        
        // CAŁOŚCIOWO
        
        $output['calosc']['indywidualne'] = $DB->selectAssocs("SELECT 
		`s_poslowie_kadencje`.`id`, 
		`s_poslowie_kadencje`.`nazwa`, 
		`s_kluby`.`id` as 'klub_id', 
		`s_kluby`.`skrot`, 
		`mowcy_poslowie`.`mowca_id`, 
		SUM(`poslowie_wyjazdy`.`koszt`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`posel_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_poslowie_kadencje` 
		ON `poslowie_wyjazdy`.`posel_id` = `s_poslowie_kadencje`.`id` 
		JOIN `s_kluby`
		ON `s_poslowie_kadencje`.`klub_id` = `s_kluby`.`id` 
		JOIN `mowcy_poslowie`
		ON `s_poslowie_kadencje`.`id` = `mowcy_poslowie`.`posel_id`
		GROUP BY `poslowie_wyjazdy`.`posel_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt`) DESC
		LIMIT 5
		");
		
		$output['calosc']['klubowe'] = $DB->selectAssocs("SELECT 
		`s_kluby`.`id`, 
		`s_kluby`.`nazwa`, 
		SUM(`poslowie_wyjazdy`.`koszt`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`klub_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_kluby`
		ON `poslowie_wyjazdy`.`klub_id` = `s_kluby`.`id`
		GROUP BY `poslowie_wyjazdy`.`klub_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt`) DESC
		LIMIT 5
		");
		
		/*
		// TRANSPORT
        
        $output['transport']['indywidualne'] = $DB->selectAssocs("SELECT 
		`s_poslowie_kadencje`.`id`, 
		`s_poslowie_kadencje`.`nazwa`, 
		`s_kluby`.`skrot`, 
		SUM(`poslowie_wyjazdy`.`koszt_transport`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`posel_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_poslowie_kadencje` 
		ON `poslowie_wyjazdy`.`posel_id` = `s_poslowie_kadencje`.`id` 
		JOIN `s_kluby`
		ON `s_poslowie_kadencje`.`klub_id` = `s_kluby`.`id`
		GROUP BY `poslowie_wyjazdy`.`posel_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt_transport`) DESC
		LIMIT 5
		");
		
		$output['transport']['klubowe'] = $DB->selectAssocs("SELECT 
		`s_kluby`.`id`, 
		`s_kluby`.`nazwa`, 
		SUM(`poslowie_wyjazdy`.`koszt_transport`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`klub_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_kluby`
		ON `poslowie_wyjazdy`.`klub_id` = `s_kluby`.`id`
		GROUP BY `poslowie_wyjazdy`.`klub_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt_transport`) DESC
		LIMIT 5
		");
		
		// HOTEL
        
        $output['hotel']['indywidualne'] = $DB->selectAssocs("SELECT 
		`s_poslowie_kadencje`.`id`, 
		`s_poslowie_kadencje`.`nazwa`, 
		`s_kluby`.`skrot`, 
		SUM(`poslowie_wyjazdy`.`koszt_hotel`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`posel_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_poslowie_kadencje` 
		ON `poslowie_wyjazdy`.`posel_id` = `s_poslowie_kadencje`.`id` 
		JOIN `s_kluby`
		ON `s_poslowie_kadencje`.`klub_id` = `s_kluby`.`id`
		GROUP BY `poslowie_wyjazdy`.`posel_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt_hotel`) DESC
		LIMIT 5
		");
		
		$output['hotel']['klubowe'] = $DB->selectAssocs("SELECT 
		`s_kluby`.`id`, 
		`s_kluby`.`nazwa`, 
		SUM(`poslowie_wyjazdy`.`koszt_hotel`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`klub_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_kluby`
		ON `poslowie_wyjazdy`.`klub_id` = `s_kluby`.`id`
		GROUP BY `poslowie_wyjazdy`.`klub_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt_hotel`) DESC
		LIMIT 5
		");
		*/
		
		return $output;
        

    }

} 