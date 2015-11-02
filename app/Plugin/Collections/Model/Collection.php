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
    );

	public function publish($id) {
		return $this->syncById($id, true);
	}

	public function unpublish($id) {
		return $this->syncById($id);
	}

	public function syncAll($public = false) {
		foreach(
			$this->query("SELECT id FROM `collections`")
			as $obj
		)
			$this->syncById($obj['collections']['id'], $public);
	}
    
    public function afterSave($created, $options) {

        if(isset($this->data['Collection']['id'])) {
            $this->syncById($this->data['Collection']['id']);
        }
	    
    }

	public function afterDelete() {
		if(isset($this->data['Collection']['id'])) {
			$id = (int) $this->data['Collection']['id'];
			$res = $this->query("SELECT id FROM objects WHERE `dataset_id`='210' AND `object_id`='" . addslashes( $id ) . "' LIMIT 1");
			$global_id = (int)(@$res[0]['objects']['id']);
			if($global_id) {
				$params = array(
					'index' => 'mojepanstwo_v1',
					'type' => 'objects',
					'id' => $global_id,
					'refresh' => true,
					'ignore' => 404
				);

				$ES = ConnectionManager::getDataSource('MPSearch');
				$ret = $ES->API->delete($params);
				$params['type'] = 'collections';
				$ret = $ES->API->delete($params);
			}
		}
	}
    
    public function deleteSync($collection) {
	    	    
	    $ES = ConnectionManager::getDataSource('MPSearch');	    
	   	   
	    $params = array();
		$params['index'] = 'mojepanstwo_v1';
		$params['type']  = 'objects';
		$params['id']    = $collection['global_id'];
		$params['refresh'] = true;
		$params['ignore'] = 404;
		
		$ret = $ES->API->delete($params);
		return $ret;
	    
	}
    
    public function syncById($id, $public = false) {
	    
	    if( !$id )
	    	return false;
	    
	    $data = $this->find('first', array(
		    'conditions' => array(
			    'Collection.id' => $id,
		    ),
	    ));
	    
	    if( $data ) {
		    
	    	return $this->syncByData( $data , $public);
	    
	    } else
	    	return false;
	    
    }
    
    public function syncByData($data, $public = false) {
	    	        
	    if( 
	    	empty($data) || 
	    	!isset($data['Collection'])
	    )
	    	return false;
	    	       	    
	    #App::import('model', 'DB');
        #$this->DB = new DB();
        
        $data = $data['Collection'];

		$res = $this->query("SELECT COUNT(*) FROM `collection_object` WHERE `collection_id`='" . $data['id'] . "'");
        $data['items_count'] = (int) (@$res[0][0]['COUNT(*)']);
		$res = $this->query("SELECT id FROM objects WHERE `dataset_id`='210' AND `object_id`='" . addslashes( $data['id'] ) . "' LIMIT 1");
		$global_id = (int)(@$res[0]['objects']['id']);

	    if( !$global_id ) {

			$this->query("INSERT INTO `objects` (`dataset`, `dataset_id`, `object_id`) VALUES ('kolekcje', 210, ".$data['id'].")");
		    $global_id = $this->getLastInsertID();
		    
	    }
	    
	    $ES = ConnectionManager::getDataSource('MPSearch');	    

	    $params = array();
		$params['index'] = 'mojepanstwo_v1';
		$params['type']  = 'collections';
		$params['id']    = $global_id;
		$params['refresh'] = true;
		$params['body']  = array(
			'title' => $data['name'],
			'text' => $data['name'],
			'dataset' => 'kolekcje',
			'slug' => Inflector::slug($data['name']),
			'date' => date('Ymd\THis\Z', strtotime($data['created_at'])),
			'id' => $data['id'],
			'nazwa' => $data['name'],
			'description' => $data['description'],
			'user_id' => $data['user_id'],
			'is_public' => $data['is_public'],
			'object_id' => $data['object_id'],
			'items_count' => $data['items_count'],
		);
		
		$ret = $ES->API->index($params);

		if($data['is_public'] == '1' || $public) {
			foreach(array('date', 'nazwa', 'description', 'user_id', 'is_public', 'object_id', 'items_count') as $f)
				unset($params['body'][$f]);

			$params['body']['data'] = array(
				'kolekcje.czas_utworzenia' => $data['created_at'],
				'kolekcje.id' => $data['id'],
				'kolekcje.nazwa' => $data['name'],
				'kolekcje.notatka' => $data['description'],
				'kolekcje.user_id' => $data['user_id'],
				'kolekcje.is_public' => $data['is_public'],
				'kolekcje.object_id' => $data['object_id'],
				'kolekcje.items_count' => $data['items_count'],
			);
			$params['type'] = 'objects';
			$ret = $ES->API->index($params);
		} else {
			$deleteParams = array();
			$deleteParams['index'] = 'mojepanstwo_v1';
			$deleteParams['type'] = 'objects';
			$deleteParams['id'] = $global_id;
			$deleteParams['refresh'] = true;
			$deleteParams['ignore'] = array(404);
			$ES->API->delete($deleteParams);
		}

		return $data['id'];	    
	    
    }

}
