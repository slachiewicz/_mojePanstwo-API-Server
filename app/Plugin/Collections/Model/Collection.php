<?php

class Collection extends AppModel {

    public $useTable = 'collections';

    public $validate = array(
        'name' => array(
            'rule' => array('minLength', '3'),
            'required' => true,
            'message' => 'Nazwa kolekcji musi zawierać przynajmniej 3 znaki'
        ),
        'user_id' => array(
            'rule' => 'notEmpty',
            'required' => true
        ),
        'description' => array(
            'rule' => array('maxLength', '16383'),
            'required' => false
        ),
        'image' => array(
            'rule' => 'numeric',
            'required' => false
        )
    );
    
    public function afterSave($created, $options) {
	    
	    $this->syncByData($this->data);
	    
    }
    
    public function syncById($id) {
	    
	    if( !$id )
	    	return false;
	    
	    $data = $this->find('first', array(
		    'conditions' => array(
			    'Collection.id' => $id,
		    ),
	    ));
	    
	    if( $data ) {
		    
	    	return $this->syncByData( $data );
	    
	    } else
	    	return false;
	    
    }
    
    public function syncByData($data) {
	    	        
	    if( 
	    	empty($data) || 
	    	!isset($data['Collection'])
	    )
	    	return false;
	    	       	    
	    App::import('model', 'DB');
        $this->DB = new DB();
        
        $data = $data['Collection'];
        
        $data['items_count'] = (int) $this->DB->selectValue("SELECT COUNT(*) FROM `collection_object` WHERE `collection_id`='" . $data['id'] . "'");
        $global_id = $this->DB->selectValue("SELECT id FROM objects WHERE `dataset_id`='210' AND `object_id`='" . addslashes( $data['id'] ) . "' LIMIT 1");
        
	    if( !$global_id ) {
		    
		    $this->DB->insertIgnoreAssoc('objects', array(
			    'dataset' => 'kolekcje',
			    'dataset_id' => 210,
			    'object_id' => $data['id'],
		    ));
		    
		    $global_id = $this->DB->_getInsertId();
		    
	    }
	    
	    $ES = ConnectionManager::getDataSource('MPSearch');	    
	   	   
	    $params = array();
		$params['index'] = 'mojepanstwo_v1';
		$params['type']  = 'objects';
		$params['id']    = $global_id;
		$params['refresh'] = true;
		$params['body']  = array(
			'id' => $data['id'],
			'title' => $data['name'],
			'text' => $data['name'],
			'dataset' => 'kolekcje',
			'slug' => Inflector::slug($data['name']),
			'data' => array(
				// 'kolekcje.czas_utworzenia' => '2015-09-24 15:01:45',
			    'kolekcje.id' => $data['id'],
			    'kolekcje.nazwa' => $data['name'],
			    'kolekcje.user_id' => $data['user_id'],
			    'kolekcje.items_count' => $data['items_count'],
			),
		);															
				
		$ret = $ES->API->index($params);
		return $data['id'];	    
	    
    }

}