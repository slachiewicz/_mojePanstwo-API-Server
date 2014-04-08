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