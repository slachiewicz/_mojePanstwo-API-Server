<?

class DataobjectsController extends AppController
{
    public $uses = array('Dane.Dataset', 'Dane.Dataobject');
	public $components = array('S3');

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

    public function search()
    {
        $search = $this->Dataobject->search('*', $this->request->query);
        $this->set('search', $search);
        $this->set('_serialize', array('search'));

    }

    public function view()
    {
   		$object = $this->Dataobject->getObject($this->request->params['alias'], $this->request->params['object_id'], $this->request->query);
		$serialize = array('object');

        // TODO co to za dziwny redirect?
		if( !$object && ($redirect = $this->Dataobject->getRedirect($this->request->params['alias'], $this->request->params['object_id'])) ) {

			$this->set('redirect', $redirect);
			$serialize[] = 'redirect';

		}

		$this->set(array(
			'object' => $object,
			'_serialize' => $serialize,
		));
    }
    
    public function feed()
    {
	    	    
	    $class = ucfirst( $this->request->params['alias'] ) . 'Object';
	    $this->loadModel('Dane.' . $class);
	    	    
	    if( class_exists($class) )
		    $model = $this->$class;
	    else
		    $model = $this->Dataobject;
	    
	    $direction = 'desc';
	    if(
			isset($this->request->query['direction']) && 
			( $this->request->query['direction'] == 'asc' )
		)
			$direction = 'asc';
	    
	    $feed = $model->getFeed($this->request->params['alias'] . '.' . $this->request->params['object_id'], array(
		    'order' => '_date ' . $direction,
	    ));
	    	     
	    $this->set(array(
			'search' => $feed,
			'_serialize' => array('search'),
		));
	    
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

    public function alertsQueries()
    {

        $id = $this->request->params['id'];
        $queries = $this->Dataobject->getAlertsQueries($id, $this->user_id);

        $this->set(array(
            'queries' => $queries,
            '_serialize' => 'queries',
        ));
    }


}