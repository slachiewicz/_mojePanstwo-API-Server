<?

class Datachannel extends AppModel
{

    public $useTable = 'datachannels';

    public $hasMany = array(
        'Dataset' => array(
            'className' => 'Dane.Dataset',
            'foreignKey' => 'channel_id',
        ),
    );
    public $actsAs = array('Containable');
    public $virtualFields = array(
        'name' => 'nazwa',
    );

    public function find($type = 'first', $queryData = array())
    {
        $catalog_field = 'backup_catalog';
        $this->hasMany['Dataset']['conditions'][$catalog_field] = '1';

        $queryData = array_merge_recursive(array(
            'order' => array('Datachannel.ord' => 'asc'),
            'limit' => 100,
        ), $queryData);

        return parent::find($type, $queryData);
    }

    public function getQueries()
    {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        debug($logs);
    }
    
    public function search($alias, $queryData = array()) {
		
		$datachannel = $this->find('first', array(
            'conditions' => array(
                'Datachannel.slug' => $alias,
            ),
        ));
				
		$filters = array();
		$facets = array('dataset');
		$order = array();
		$q = false;
		$mode = (isset($queryData['mode']) && $queryData['mode']) ? $queryData['mode'] : null;
		$limit = (isset($queryData['limit']) && $queryData['limit']) ? $queryData['limit'] : 20;
		$page = (isset($queryData['page']) && $queryData['page']) ? $queryData['page'] : 1;
		
		
		$facets_dict = array();
		foreach( $datachannel['Dataset'] as $dataset ) {
		
			$filters['dataset'][] = $dataset['base_alias'];
			$dataset_dict[ $dataset['base_alias'] ] = $dataset;
		
		}
		
		
		if( isset($queryData['conditions']) && is_array($queryData['conditions']) ) {
			foreach( $queryData['conditions'] as $key => $value ) {
				
				if( in_array($key, array('page', 'limit')) )
					continue;
				
				if( $key[0]=='!' )
					$switchers[ substr($key, 1) ] = $value;
				elseif( $key=='q' )
					$q = $value;
				else {
					$filters[ $key ] = array($value, in_array($key, $virtual_fields));
				}
			
			}
		}
		
		
		
		if( isset($queryData['q']) )
			$q = $queryData['q'];
		
		
		
		if( isset($queryData['order']) && $queryData['order'] )
			$order = $queryData['order'];	
		
		$_order = array();
		foreach( $order as $o ) {
			$parts = explode(' ', $o);
			if( in_array($parts[0], array('_date', '_title')) )
				$_order[] = $o;
		}
		
		
		App::import('model','Dane.Dataobject');
		$this->Dataobject = new Dataobject();
        $search = $this->Dataobject->find('all', array(
        	'q' => $q,
        	'mode' => $mode,
        	'filters' => $filters,
        	'facets' => $facets,
        	'order' => $_order,
        	'limit' => $limit,
        	'page' => $page,
        ));
		
		
		
		if( isset($search['facets']) ) {
						
			$facets = array();

			foreach( $search['facets'] as $field => $buckets ) {
				
				if( $field == 'dataset' ) {
									
					$buckets = $buckets[ 0 ];
					$options = array();
					
					foreach( $buckets as $b )
						$options[] = array(
							'id' => $b['key'],
							'count' => $b['doc_count'],
							'label' => $dataset_dict[ $b['key'] ]['name'],
						);
														        
			        $facets[] = array(
			        	'field' => 'dataset',
			        	'typ_id' => '1',
			        	'parent_field' => false,
			        	'label' => 'Zbiór danych',
			        	'desc' => false,
			        	'params' => array(
			        		'options' => $options,
			        	),
			        );
		        
		        }
				
			}
			
			$search['facets'] = $facets;
			
		}
		
		/*
		'field' => 'autor_id',
		'typ_id' => '1',
		'parent_field' => '',
		'label' => 'Organ wydający',
		'desc' => '',
		'params' => array(
		*/
		
				
		return $search;
	    
    }

}