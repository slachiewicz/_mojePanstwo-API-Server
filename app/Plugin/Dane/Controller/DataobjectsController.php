<?

class DataobjectsController extends AppController
{
	
	public $components = array('S3');
	
	public function suggest()
    {
    	
    	$q = (string) @$this->request->query['q'];
    	
        $objects = $this->Dataobject->search('*', array(
        	'conditions' => array(
        		'q' => $q,
        	),
        	'mode' => 'suggester_main',
        	'limit' => 5,
        ));
        
        $_dict = array(
        	'administracja_publiczna' => 'Instytucja',
        	'bdl_wskazniki' => 'Wskaźniki',
        	'bdl_wskazniki_grupy' => 'Wskaźniki',
        	'bdl_wskazniki_kategorie' => 'Wskaźniki',
        	'ustawy' => 'Ustawa',
        	'krs_podmioty' => 'Organizacja',
        	'krs_osoby' => 'Osoba',
        	'twitter' => 'Tweet', 
        	'twitter_accounts' => 'Twitter',     
        	'poslowie' => 'Poseł',
        	'zamowienia_publiczne' => 'Zamówienie',  
        	'gminy' => 'Gmina',	
        );
        
        if( isset($objects['dataobjects']) && !empty($objects['dataobjects']) )
	        foreach( $objects['dataobjects'] as &$obj )
		        $obj['label'] = @$_dict[ $obj['dataset'] ];
        
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