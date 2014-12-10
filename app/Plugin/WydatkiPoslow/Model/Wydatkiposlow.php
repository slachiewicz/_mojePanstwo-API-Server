<?php

class Wydatkiposlow extends AppModel
{

    public $useTable = false;

    public function getStats()
    {

        App::import('model', 'Dane.Dataobject');
        $this->Dataset = new Dataset();
		
		$data = $this->Dataset->search('poslowie_biura_wydatki');
		
		return array(
			'biura' => $data['dataobjects'],
		);
        
    }
    
    public function getCategory($id)
    {
	    
	    return 'asdfg';
	    
    }
    
}
