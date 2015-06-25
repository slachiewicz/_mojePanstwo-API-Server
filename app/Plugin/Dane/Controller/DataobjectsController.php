<?

class DataobjectsController extends AppController
{
    public $uses = array('Dane.Dataobject');
	public $components = array('S3');
	
	public function index($dataset = false) {
		
		if( $this->request->is('post') ) 
			$this->request->query = array_merge($this->request->query, $this->request->data);
		
		$this->_index(array(
			'dataset' => $dataset
		));
	}
	
	public function suggest() {
		
		$hits = array();
		
		if(
			isset( $this->request->query['q'] ) && 
			( $q = trim($this->request->query['q']) )
		) {
			
			$params = array(
				'dataset' => false,
			);
			
			if(
				isset($this->request->query['dataset']) &&
	            ($dataset = $this->request->query['dataset'])
			)
				$params['dataset'] = $dataset;
			
			$hits = $this->Dataobject->getDataSource()->suggest($q, $params);
			
		}
		
		$this->set('hits', $hits);
		$this->set('_serialize', 'hits');
		
	}
	
	public function feed($dataset, $id = false)
    {	    
		
		if( $dataset == 'user' ) {
			
			$feed_params = array(
				'user_type' => $this->Auth->user('type'),
				'user_id' => $this->Auth->user('id'),
			);
						
		} else {
		
			$feed_params = array(
				'dataset' => $dataset,
				'object_id' => $id,
			);
			
			if( isset($this->request->query['channel']) )
				$feed_params['channel'] = $this->request->query['channel'];
		
		}
				
	    $this->_index(array(
		    '_feed' => $feed_params,
	    ));
	    
    }


	private function _index($params = array()){
		
		$query = $this->request->query;
				
		if( isset($params['dataset']) && $params['dataset'] )
			$query['conditions']['dataset'] = $params['dataset'];
		
		
		if( isset($params['_main']) && $params['_main'] )
			$query['conditions']['_main'] = true;
			
		if( isset($params['_feed']) && $params['_feed'] )
			$query['conditions']['_feed'] = $params['_feed'];		
		
		if( isset( $query['conditions']['subscribtions'] ) && $query['conditions']['subscribtions'] ) {
						
			$query['conditions']['subscribtions'] = array(
				'user_type' => $this->Auth->user('type'),
				'user_id' => $this->Auth->user('id'),
			); 
			
		}
				
		$objects = $this->Dataobject->find('all', $query);
		$count = ( 
			( $lastResponse = $this->Dataobject->getDataSource()->lastResponse ) && 
			isset( $lastResponse['hits'] ) && 
			isset( $lastResponse['hits']['total'] ) 
		) ? $lastResponse['hits']['total'] : null;
		
		$took = ( 
			( $lastResponse = $this->Dataobject->getDataSource()->lastResponse ) && 
			isset( $lastResponse['took'] ) 
		) ? $lastResponse['took'] : null;
		
		$_serialize = array('Dataobject', 'Count', 'Took');
		
		if( !empty($this->Dataobject->getDataSource()->Aggs) ) {
			// debug($this->Dataobject->getDataSource()->Aggs['typ_id']); die();
			$this->set('Aggs', $this->Dataobject->getDataSource()->Aggs);
			$_serialize[] = 'Aggs';
		}
		
				
		$this->set('Dataobject', $objects);
		$this->set('Count', $count);
		$this->set('Took', $took);
        $this->set('_serialize', $_serialize);
		
	}
	
    public function view($dataset, $id)
    {

	    $query = $this->request->query;
	    	    
	    $layers = array();
	    if( isset($query['layers']) ) {
		    $layers = $query['layers'];
		    unset( $query['layers'] );
	    }
					    	    
	    $query['conditions']['dataset'] = $dataset;
	    $query['conditions']['id'] = $id;
	        
	    $object = $this->Dataobject->find('first', $query);
	    
	    
	    
	    
	    if( !$object ) {
		    
		    throw new NotFoundException();
		    
	    }
	    
	    $this->Dataobject->data = $object;
	    
	    $_serialize = array('Dataobject');
	    
	    if( !empty($this->Dataobject->getDataSource()->Aggs) ) {
			// debug($this->Dataobject->getDataSource()->Aggs); die();
			$this->set('Aggs', $this->Dataobject->getDataSource()->Aggs);
			$_serialize[] = 'Aggs';
		}
	    			
		if( !empty($layers) ) {
			
			if( is_string($layers) )
				$layers = array($layers);
			
			$this->loadModel('Dane.DatasetChannel');
			$this->loadModel('Dane.Subscription');
						
			foreach( $layers as $layer ) {
								
				if( $layer=='page' ) {
					
					$page = array(
						'cover' => true,
						'logo' => false,
					);
					
					if( $this->Auth->user('type')=='account' ) {
						
						$this->loadModel('Dane.ObjectUser');
						$page['roles'] = $this->ObjectUser->find('first', array(
							'fields' => 'role',
							'conditions' => array(
								'ObjectUser.dataset' => $dataset,
								'ObjectUser.object_id' => $id,
								'ObjectUser.user_id' => $this->Auth->user('id'),
							),
						));
					}
					
					$object['layers']['page'] = $page;
				
				} elseif( $layer=='channels' ) {
										
					$object['layers']['channels'] = $this->DatasetChannel->find('all', array(
						'fields' => array('channel', 'title', 'subject_dataset'),
						'conditions' => array(
							'creator_dataset' => $object['dataset'],
						),
						'order' => 'ord asc',
					));
					
					$object['layers']['subscription'] = $this->Subscription->find('first', array(
						'conditions' => array(
							'user_type' => $this->Auth->user('type'),
							'user_id' => $this->Auth->user('id'),
							'dataset' => $object['dataset'],
							'object_id' => $object['id'],
						)
					));
																									
				} else {
					$object['layers'][ $layer ] = $this->Dataobject->getObjectLayer($dataset, $id, $layer);
				}
			}
			
		}
			
	    	    
	    
		$this->set(array(
			'Dataobject' => $object,
			'_serialize' => $_serialize,
		));
    }
    
    public function view_layer()
    {
	    $this->loadModel('Dane.Dataset');
        $dataset = $this->Dataset->find('first', array(
            'conditions' => array(
                'Dataset.alias' => $this->request->params['alias'],
            )));

        $layer = $this->request->params['layer'];
        $matching_layers = array_filter($dataset['Layer'], function($l) use($layer) {return $l['layer'] == $layer;});

        if (empty($dataset) || empty($matching_layers)) {
            throw new NotFoundException();
        }

        $layer = $this->Dataobject->getObjectLayer($this->request->params['alias'], $this->request->params['object_id'], $layer);

        $this->setSerialized('layer', $layer);
    }

    public function layer()
    {

        $alias = $this->request->params['alias'];
        $id = $this->request->params['object_id'];
        $layer = $this->request->params['layer'];
        $params = array_merge($this->request->query, $this->data);

        if (!$alias || !$id || !$layer)
            return false;

        $layer = $this->Dataobject->getObjectLayer($alias, $id, $layer, $params);

        $this->set(array(
            'layer' => $layer,
            '_serialize' => 'layer',
        ));
    }
	
	/*
    public function alertsQueries()
    {

        $id = $this->request->params['id'];
        $queries = $this->Dataobject->getAlertsQueries($id, $this->user_id);

        $this->set(array(
            'queries' => $queries,
            '_serialize' => 'queries',
        ));
    }
    */
	
	public function subscribe()
	{
		
		$this->Auth->deny();
		
		$status = $this->Dataobject->subscribe(array(
			'dataset' => $this->request->params['dataset'],
			'id' => $this->request->params['id'],
			'user_type' => $this->Auth->user('type'),
			'user_id' => $this->Auth->user('id'),
		));
		
		$this->set('status', $status);
		$this->set('_serialize', array('status'));
		
	}
	
	public function unsubscribe()
	{
		
		$this->Auth->deny();
		
		$status = $this->Dataobject->unsubscribe(array(
			'dataset' => $this->request->params['dataset'],
			'id' => $this->request->params['id'],
			'user_type' => $this->Auth->user('type'),
			'user_id' => $this->Auth->user('id'),
		));
		
		$this->set('status', $status);
		$this->set('_serialize', array('status'));
		
	}

}