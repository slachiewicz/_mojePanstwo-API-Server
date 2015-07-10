<?
App::uses('AppController', 'Controller');
App::uses('MPSearch', 'Model/Datasource');
App::uses('MpUtils\Url', 'Lib');

class DataobjectsController extends AppController
{
    public $uses = array('Dane.Dataobject');
	public $components = array('S3');
	
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
		$original_query = $query = array_intersect_key($this->request->query, array_flip($allowed_query_params));

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

		// TODO move RESULTS_COUNT_MAX here
		if (isset($query['limit'])) {
			if ($query['limit'] > MPSearch::RESULTS_COUNT_MAX) {
				$query['limit'] = MPSearch::RESULTS_COUNT_MAX;
			}
		} else {
			$query['limit'] = MPSearch::RESULTS_COUNT_DEFAULT;
		}

		$_items = $this->Dataobject->find('all', $query);

		$processed_query = $this->Dataobject->buildQuery('all', $query);
		$page = $processed_query['page']; // starts with 1

		$lr_stats = @$this->Dataobject->getDataSource()->lastResponseStats;
		$count = $lr_stats['count'];

		$_meta = array(
			'page' => $page,
			'max_results' => MPSearch::RESULTS_COUNT_MAX,
			'total' => $count,
			'took_ms' => $lr_stats['took_ms']
		);

		// HATEOS
		$current_url = Router::url(null, true);
		$_links = array(
			'self' => $current_url
		);

		$url = new MpUtils\Url($current_url);
		$url->setParams($original_query);

		if ($page > 1) {
			$url->setParam('page', 1);
			$_links['first'] = $url->buildUrl();

			$url->setParam('page', $page - 1);
			$_links['prev'] = $url->buildUrl();
		}

		$lastPage = (int) (($count - 1) / $processed_query['limit']) + 1;
		if ($page < $lastPage) {
			$url->setParam('page', $lastPage);
			$_links['last'] = $url->buildUrl();

			$url->setParam('page', $page + 1);
			$_links['next'] = $url->buildUrl();
		}

		// TODO is aggs needed?
		if( !empty($this->Dataobject->getDataSource()->Aggs) ) {
			// debug($this->Dataobject->getDataSource()->Aggs['typ_id']); die();
			$this->set('Aggs', $this->Dataobject->getDataSource()->Aggs);
			$_serialize[] = 'Aggs';
		}

        $this->setSerialized(compact('_items', '_links', '_meta'));
	}

    public function view($dataset, $id)
    {
		$doquery = array(
			'conditions' => array(
				'dataset' => $dataset,
				'id' => $id
			)
		);
	        
	    $object = $this->Dataobject->find('first', $doquery);
	    
	    if( !$object ) {
		    throw new NotFoundException();
	    }
	    
	    $this->Dataobject->data = $object;

	    if(
		    isset( $object['global_id'] ) && 
	    	$this->Auth->user() && 
	    	( $subscribtion = $this->Dataobject->checkSubscribtion(array(
		    	'global_id' => $object['global_id'],
		    	'user_type' => $this->Auth->user('type'),
		    	'user_id' => $this->Auth->user('id'),
	    	)) )
	    ) {
		    // TODO czy to wykorzystywane?
		    $object['subscribtion'] = true;
		    
	    }


		// LAYERS
		// load list of layers
		// TODO


		// what should we load?
		$query = $this->request->query;
		$layers_to_load = array();
		if( isset($query['layers']) ) {
			$layers_to_load = $query['layers'];
		}

		if( !empty($layers_to_load) ) {
			if( is_string($layers_to_load) ) {
				$layers_to_load = array($layers_to_load);
			}
			
			$this->loadModel('Dane.DatasetChannel');
			
			foreach( $layers_to_load as $layer ) {
				// ToDO tych layers standardowych nie rozpoznaje

				if ($layer == 'dataset') {
					// TODO zaciągnąć obsługę, zrobic mockowanego redisa

				} elseif ($layer == 'channels') {

					$object['layers']['channels'] = array_map(function ($ch) {
						return $ch['DatasetChannel'];
					},
						$this->DatasetChannel->find('all', array(
						'fields' => array('channel', 'title', 'subject_dataset'),
						'conditions' => array(
							'creator_dataset' => $object['dataset'],
						),
						'order' => 'ord asc',
					)));

				} elseif ($layer == 'subscriptions') {

					$this->loadModel('Dane.Subscription');

					$subscriptions = array();
					foreach ($this->Subscription->find('all', array(
						'fields' => array(
							'Subscription.id', 'Subscription.title', 'Subscription.url'
						),
						'conditions' => array(
							'user_type' => $this->Auth->user('type'),
							'user_id' => $this->Auth->user('id'),
							'dataset' => $dataset,
							'object_id' => $id,
						),
					)) as $sub)
						$subscriptions[] = $sub['Subscription'];


					$object['layers']['subscriptions'] = $subscriptions;

				} else {
					$object['layers'][$layer] = $this->Dataobject->getObjectLayer($dataset, $id, $layer);
				}
			}
		}

		$this->setSerialized('object', $object);
    }

	// TODO testy
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
}