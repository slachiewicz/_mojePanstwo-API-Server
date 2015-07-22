<?php

class OrganizacjeDzialania extends AppModel {

    public $useTable = 'organizacje_dzialania';
    public $uses = array('Dane.Dataobject');
    
    public function afterSave($created, $options = array()) {
	    	  
		if( $id = $this->data['OrganizacjeDzialania']['id'] )
	   		$this->sync($id);
		    	    
    }
	
	public function sync($id) {
	
		if( $id ) {
	   	  
		    $this->query("INSERT IGNORE INTO `objects` (`dataset`, `dataset_id`, `object_id`) VALUES ('dzialania', '199', '" . addslashes( $id ) . "')");
			    
		    $this->objectIndex(array(
			    'dataset' => 'dzialania',
			    'object_id' => $id,
		    ));
	    
	    }
	
	}

}