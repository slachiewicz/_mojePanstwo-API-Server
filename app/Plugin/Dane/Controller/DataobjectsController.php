<?

class DataobjectsController extends AppController
{
	
	public $components = array('S3');
	
    public function search()
    {
        $objects = $this->Dataobject->find('all', $this->data);
        $this->set('objects', $objects);
        $this->set('_serialize', array('objects'));

    }

    public function view()
    {
   		$object = $this->Dataobject->getObject($this->request->params['alias'], $this->request->params['object_id'], $this->request->query);
		$serialize = array('object');
		
		if( !$object && ($redirect = $this->Dataobject->getRedirect($this->request->params['alias'], $this->request->params['object_id'])) ) {
			
			$this->set('redirect', $redirect);
			$serialize[] = 'redirect';
			
		}
		
		$this->setSerialized('object', $object);
    }

    public function layer()
    {

        $alias = $this->request->params['alias'];
        $id = $this->request->params['object_id'];
        $layer = $this->request->params['layer'];
        $params = $this->data;
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