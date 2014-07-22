<?

class DatasetsController extends AppController
{
    public $uses = array('Dane.Dataset', 'Dane.DatasetsGroup');

    public function catalog()
    {
        $datasets = $this->Dataset->DatasetsGroup->find('all', array(
                'order' => array('DatasetsGroup.ord' => 'asc'),
            )
        );
        $this->set('datasets', $datasets);
        $this->set('_serialize', array('datasets'));
    }

    public function index()
    {
        $catalog_field = 'backup_catalog';
        $catalog_field = 'Dataset.' . $catalog_field;

        $datasets = $this->Dataset->find('all', array(
                'fields' => array(
                    'Dataset.id',
	                'Dataset.name',
                    'Dataset.opis',
                    'Dataset.alias',
                    'Dataset.base_alias',
                    'Dataset.count',
	                'Dataset.class',
	                'Dataset.channel_id',
                ),
                'conditions' => array(
                    $catalog_field => "1",
                ),
            )
        );
        $this->set('datasets', $datasets);
        $this->set('_serialize', array('datasets'));
    }

    public function info()
    {
				
        $alias = @addslashes($this->request->params['alias']);
        
        $dataset = $this->Dataset->find('first', array(
                'conditions' => array(
                    'Dataset.alias' => $alias,
                ),
            )
        );
        
        if( isset($this->request->query['full']) && $this->request->query['full'] ) 
	        $dataset = array_merge($dataset, array(
	        	'switchers' => $this->Dataset->getSwitchers($alias, true),
	        	'filters' => $this->Dataset->getFilters($alias, true),
	        	'orders' => $this->Dataset->getSortings($alias),
	        ));
	        
        $this->set('dataset', $dataset);
        $this->set('_serialize', array('dataset'));
    }

    public function search()
    {
        $alias = @addslashes($this->request->params['alias']);

        $this->loadModel('Dane.Dataobject');

        $queryData = $this->request->query;
        $queryData['conditions']['dataset'] = $alias;

        $search = $this->Dataobject->find('all', $queryData);
        $this->set('search', $search);
        $this->set('_serialize', array('search'));
    }

    public function filters()
    {

        $alias = @addslashes($this->request->params['alias']);
        if (isset($this->data['full'])) {
            $full = $this->data['full'];
        } else {
            $full = true;
        }
        if (isset($this->data['exclude'])) {
            $exclude = $this->data['exclude'];
        } else {
            $exclude = null;
        }
        $this->set('filters', $this->Dataset->getFilters($alias, $full, $exclude));
        $this->set('_serialize', array('filters'));

    }

    public function switchers()
    {

        $alias = @addslashes($this->request->params['alias']);
        if (isset($this->data['full'])) {
            $full = $this->data['full'];
        } else {
            $full = false;
        }

        if (isset($this->data['exclude'])) {
            $exclude = $this->data['exclude'];
        } else {
            $exclude = null;
        }

        $this->set('switchers', $this->Dataset->getSwitchers($alias, $full, $exclude));
        $this->set('_serialize', array('switchers'));

    }

    public function sortings()
    {

        $alias = @addslashes($this->request->params['alias']);
        $this->set('sortings', $this->Dataset->getSortings($alias));
        $this->set('_serialize', array('sortings'));

    }
    
    public function map()
    {

        $alias = @addslashes($this->request->params['alias']);
        $page = @addslashes($this->request->query['page']);

        $this->set('map', $this->Dataset->getMap($alias, $page));
        $this->set('_serialize', array('map'));

    }

}