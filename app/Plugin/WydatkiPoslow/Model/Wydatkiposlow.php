<?php

class Wydatkiposlow extends AppModel
{

    public $useTable = false;

    public function getStats()
    {

        App::import('model', 'DB');
        $DB = new DB();
		
		$biura = $DB->selectAssocs("
		SELECT `s_poslowie_rozliczenia_pola`.`id` , `s_poslowie_rozliczenia_pola`.`tytul`, `s_poslowie_rozliczenia_pola`.`skrot` , SUM( `s_poslowie_rozliczenia-pola`.`wartosc` ) AS `wartosc` 
		FROM `s_poslowie_rozliczenia-pola`
		JOIN `s_poslowie_rozliczenia` ON `s_poslowie_rozliczenia-pola`.`rozliczenie_id` = `s_poslowie_rozliczenia`.`id`
		JOIN `s_poslowie_rozliczenia_pola` ON `s_poslowie_rozliczenia_pola`.`id` = `s_poslowie_rozliczenia-pola`.`pole_id`
		WHERE `s_poslowie_rozliczenia`.`rocznik` = 2013
		GROUP BY `s_poslowie_rozliczenia-pola`.`pole_id`
		ORDER BY `id` ASC 
		");
		
		return array(
			'biura' => $biura,
		);
        
    }
    
    public function getCategory($id)
    {
	    
	    return 'asdfg';
	    
    }
    
}
