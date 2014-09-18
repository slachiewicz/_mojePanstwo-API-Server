<?php

class Finanse extends AppModel
{

    public $useTable = false;

    public function getBudgetSpendings()
    {
		
		App::import('model','DB');
		$DB = new DB();
		
		$data = $DB->selectAssocs("
			SELECT 
				`pl_budzety_wydatki_dzialy`.`id` as 'dzial_id',  
				`pl_budzety_wydatki_dzialy`.`tresc` as 'dzial_nazwa',  
				`pl_budzety_wydatki_dzialy`.`plan` as 'dzial_plan', 
				`pl_budzety_wydatki_rozdzialy`.`id` as 'rozdzial_id',  
				`pl_budzety_wydatki_rozdzialy`.`tresc` as 'rozdzial_nazwa',  
				`pl_budzety_wydatki_rozdzialy`.`plan` as 'rozdzial_plan' 
			FROM `pl_budzety_wydatki_dzialy` 
			JOIN `pl_budzety_wydatki_rozdzialy` ON `pl_budzety_wydatki_rozdzialy`.`dzial_id` = `pl_budzety_wydatki_dzialy`.`id` 
			ORDER BY
				`pl_budzety_wydatki_dzialy`.`plan` DESC, 
				`pl_budzety_wydatki_rozdzialy`.`plan` DESC
		");
		
		$dzialy = array();
		foreach( $data as $d ) {
			
			$dzialy[ $d['dzial_id'] ]['id'] = $d['dzial_id'];
			$dzialy[ $d['dzial_id'] ]['nazwa'] = $d['dzial_nazwa'];
			$dzialy[ $d['dzial_id'] ]['plan'] = $d['dzial_plan'];
			$dzialy[ $d['dzial_id'] ]['rozdzialy'][] = array(
				'id' => $d['rozdzial_id'],
				'nazwa' => $d['rozdzial_nazwa'],
				'plan' => $d['rozdzial_plan'],
			);
			
		}
		
		$dzialy = array_values( $dzialy );		
		
		return array(
			'dzialy' => $dzialy,
		);
		
    }

} 