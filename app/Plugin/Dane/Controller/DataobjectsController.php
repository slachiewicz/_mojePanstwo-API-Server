<?

class DataobjectsController extends AppController
{
    public $uses = array('Dane.Dataset', 'Dane.Dataobject');
	public $components = array('S3');
	
	/*
	public function suggest()
    {

    	$q = (string) @$this->request->query['q'];
    	$app = (string) @$this->request->query['app'];

    	$conditions = array(
    		'q' => $q,
    	);

		if( $app )
			$conditions['_app'] = $app;

        $objects = $this->Dataobject->search('*', array(
        	'conditions' => $conditions,
        	'mode' => 'suggester_main',
        	'limit' => 5,
        ));

        $this->set('objects', $objects);
        $this->set('_serialize', array('objects'));

    }
    */
	
	public function index($dataset = false)
	{
		
		$query = $this->request->query;
				
		if( $dataset ) {
			
			$query['conditions']['dataset'] = $dataset;
			
		} else {
			
			$query['conditions']['_main'] = true;
			
		}
		
		if(
			isset( $query['conditions'] ) && 
			isset( $query['conditions']['subscribtions'] ) && 
			$query['conditions']['subscribtions']
		) {
						
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
						
			foreach( $layers as $layer ) {
								
				if( $layer=='dataset' ) {

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
    
    public function feed($dataset, $id)
    {
	    
	    $query = $this->request->query;
	    if( !isset($query['conditions']) )
	    	$query['conditions'] = array();
	    
	    $query['conditions']['_feed'] = $dataset . '.' . $id;
	    
	    if( isset($query['channel']) )
	    	$query['conditions']['_feed'] .= ':' . $query['channel'];
	    	    	    
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
		
		/*
		if( !empty($this->Dataobject->getDataSource()->Aggs) ) {
			// debug($this->Dataobject->getDataSource()->Aggs['typ_id']); die();
			$this->set('Aggs', $this->Dataobject->getDataSource()->Aggs);
			$_serialize[] = 'Aggs';
		}
		*/
		
				
		$this->set('Dataobject', $objects);
		$this->set('Count', $count);
		$this->set('Took', $took);
        $this->set('_serialize', $_serialize);
	    
    }

    public function view_layer()
    {
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