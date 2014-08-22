<?

class DatasetsController extends AppController
{
    public $uses = array('Dane.Dataset');

    public function index()
    {
        $catalog_field = 'main_search';
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
                'full' => (boolean) ( isset($this->request->query['full']) && $this->request->query['full'] ),
            )
        );
	        
        $this->set('dataset', $dataset);
        $this->set('_serialize', array('dataset'));
    }

    public function search()
    {
          
        $alias = @addslashes($this->request->params['alias']);                
        $this->set('search', $this->Dataset->search($alias, $this->request->query));
        $this->set('_serialize', array('search'));
    }

    public function filters()
    {

        $alias = @addslashes($this->request->params['alias']);
        if (isset($this->request->query['full'])) {
            $full = $this->request->query['full'];
        } else {
            $full = true;
        }
        if (isset($this->request->query['exclude'])) {
            $exclude = $this->request->query['exclude'];
        } else {
            $exclude = null;
        }
        $this->set('filters', $this->Dataset->getFilters($alias, $full, $exclude));
        $this->set('_serialize', array('filters'));

    }

    public function fields()
    {
        $alias = @addslashes($this->request->params['alias']);

        $response = $this->Dataset->getFields($alias, false);

        $fields = array_map(function($el) { return $el['fields']; }, $response);

        $this->setSerialized('fields', $fields);
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