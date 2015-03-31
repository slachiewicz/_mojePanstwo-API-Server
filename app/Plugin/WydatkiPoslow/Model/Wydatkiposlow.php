<?php

class Wydatkiposlow extends AppModel
{

    public $useTable = false;

    public function getStats()
    {
		
		App::import('model', 'Dane.Dataobject');
        $Dataobject = new Dataobject();
        		
		$data = $Dataobject->find('all', array(
			'conditions' => array(
				'dataset' => 'poslowie_biura_wydatki'
			),
		));
				
		return array(
			'biura' => $data,
		);        
        
    }
    
    public function getCategory($id)
    {
	    
	    return 'asdfg';
	    
    }
    
}
