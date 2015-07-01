<?

class DataobjectsController extends AppController
{
    public $uses = array('Dane.Dataobject');
	public $components = array('S3');

	const MAX_RESULTS = 50; // TODO ile jest teraz?
	
	public function index($dataset) {
		
		$this->_index(array(
			'dataset' => $dataset
		));
	}

	// TODO co zwraca feed?
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


	private function _index($params = array()) {

		// TODO validate query before passing
		// 'recursive', 'fields',  'order', 'callbacks', 'aggs' ?
		$allowed_query_params = array('conditions', 'limit', 'page', 'fields');
		$query = array_intersect_key($this->request->query, array_flip($allowed_query_params));

		if (!is_numeric($query['limit']) || intval($query['limit']) > self::MAX_RESULTS) {
			$query['limit'] = self::MAX_RESULTS;
		}
		if (array_key_exists('fields', $query) and !is_array($query['fields'])) {
			// specyfing one field without array notation is ok
			$query['fields'] = array($query['fields']);
		}
				
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
		
		$_items = $this->Dataobject->find('all', $query);

		$processed_query = $this->Dataobject->buildQuery('all', $query);
		$page = $processed_query['page']; // starts with 1

		$count = (
			( $lastResponse = $this->Dataobject->getDataSource()->lastResponse ) && 
			isset( $lastResponse['hits'] ) && 
			isset( $lastResponse['hits']['total'] ) 
		) ? $lastResponse['hits']['total'] : null;

		$_meta = array(
			'page' => $page,
			'max_results' => self::MAX_RESULTS,
			'total' => $count
		);

// HATEOAS TODO
//		$_links = array();
//		if ($page > 1) {
//			$_links['first'] = 'TODO'; //TODO
//			$_links['prev'] = 'TODO';
//		}
//
//		$last_page = (int) (($count - 1) / $processed_query['limit']) + 1;
//		if ($page < $last_page) {
//			$_links['last'] = 'TODO'. $last_page; // TODO
//			$_links['next'] = 'TODO'. ($page + 1);
//		}

		// TODO is took and aggs needed?
		$took = (
			( $lastResponse = $this->Dataobject->getDataSource()->lastResponse ) &&
			isset( $lastResponse['took'] )
		) ? $lastResponse['took'] : null;

		if( !empty($this->Dataobject->getDataSource()->Aggs) ) {
			// debug($this->Dataobject->getDataSource()->Aggs['typ_id']); die();
			$this->set('Aggs', $this->Dataobject->getDataSource()->Aggs);
			$_serialize[] = 'Aggs';
		}

		// TODO _items, _links (self, last, parent, next), _meta (page,max_results, total)
        $this->setSerialized(compact('_items', '_links', '_meta'));
	}
	
    public function view($dataset, $id)
    {
		// TODO wyczyscic!!! jakie inne parametry tu mogą wskoczyć, czemu pozwalamy na pełne przekierowanie?
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
	    	    
	    if(
		    isset( $object['global_id'] ) && 
	    	$this->Auth->user() && 
	    	( $subscribtion = $this->Dataobject->checkSubscribtion(array(
		    	'global_id' => $object['global_id'],
		    	'user_type' => $this->Auth->user('type'),
		    	'user_id' => $this->Auth->user('id'),
	    	)) )
	    ) {
		    
		    $object['subscribtion'] = true;
		    
	    }
	    
	    			
		if( !empty($layers) ) {
			
			if( is_string($layers) )
				$layers = array($layers);
			
			$this->loadModel('Dane.DatasetChannel');
			
			foreach( $layers as $layer ) {
								
				if( $layer=='dataset' ) {
				
				} elseif( $layer=='channels' ) {
										
					$object['layers']['channels'] = $channels = $this->DatasetChannel->find('all', array(
						'fields' => array('channel', 'title', 'subject_dataset'),
						'conditions' => array(
							'creator_dataset' => $object['dataset'],
						),
						'order' => 'ord asc',
					));
															
				} elseif( $layer=='subscriptions' ) {
					
					$this->loadModel('Dane.Subscription');
															
					$subscriptions = array();
					foreach( $this->Subscription->find('all', array(
						'fields' => array(
							'Subscription.id', 'Subscription.title', 'Subscription.url'
						),
						'conditions' => array(
							'user_type' => $this->Auth->user('type'),
							'user_id' => $this->Auth->user('id'),
							'dataset' => $dataset,
							'object_id' => $id,
						),
					)) as $sub )
						$subscriptions[] = $sub['Subscription'];
						
					
					$object['layers']['subscriptions'] = $subscriptions;
														
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