<?php

App::uses('AppModel', 'Model');

class PrawoHasla extends AppModel {

    public $useTable = 'ISAP_hasla';

    public function merge($data, $id = false) {
	    
	    if(
		    $id && 
	    	isset($data['instytucja_id']) && 
	    	$data['instytucja_id']
	    ) {
		    
		    $this->save(array(
			    'id' => $id,
			    'instytucja_id' => $data['instytucja_id'],
		    ));
		    
		    $this->objectIndex(array(
			    'dataset' => 'prawo_hasla',
			    'object_id' => $id,
		    ));
		    
		    return true;
		    
	    }
	    
	    return false;
	    
    }
}