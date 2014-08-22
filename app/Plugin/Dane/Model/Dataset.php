<?

class Dataset extends AppModel
{

    public $useTable = 'datasets';
    public $belongsTo = array(
        'Datachannel' => array(
            'className' => 'Dane.Datachannel',
            'foreignKey' => 'channel_id',
        ),
		'App' => array(
            'className' => 'Application',
            'foreignKey' => 'app_id',
        ),
    );
    public $hasMany = array(
        'Layer' => array(
            'className' => 'Dane.Layer',
        )
    );

    public $actsAs = array('Containable');
    public $virtualFields = array(
        'alias' => 'base_alias',
        'class' => 'SUBSTRING(results_class, 4)',
    );

    public function find($type = 'first', $queryData = array())
    {
				
		if( 
			( $type == 'first' ) && 
			( ($alias = @$queryData['conditions']['Dataset.alias']) || ($alias = @$queryData['conditions']['Dataset.base_alias']) ) 
		) {
			
			App::import('model', 'MPCache');
	        $this->MPCache = new MPCache();
	 	        
	        	        
			return $this->MPCache->getDataset( $alias, @$queryData['full'] );
			
		}
		
        $fields = array();
        if (isset($queryData['fields']))
            $fields = $queryData['fields'];
        else {
            $fields = array(
                'Dataset.id',
                'Dataset.alias',
                'Dataset.name',
                'Dataset.class',
                'Dataset.count',
                'Dataset.channel_id',
                'Dataset.default_sort',
                'Datachannel.id',
                'Datachannel.nazwa',
                'Datachannel.slug',
                'App.id',
                'App.name',
                'App.plugin',
                'App.slug',
            );
        }

        $queryData = array_merge_recursive(array(
            'fields' => $fields,
            'order' => array('Dataset.ord' => 'asc'),
            'limit' => 100,
        ), $queryData);

        return parent::find($type, $queryData);
    }

    public function getFilters($alias, $full = true, $exclude_alias = null)
    {

        $fields = array('`filter`.`field`', '`filter`.`typ_id`');

        if ($full)
            $fields = array_merge($fields, array('`filter`.`parent_field`', '`filter`.`label`', '`filter`.`desc`'));


        // $fq = "CONCAT( `field`.`alias` , '.', `field`.`field` )";
        $q = "SELECT " . implode(", ", $fields) . " 
        FROM `datasets_filters` AS filter 
        WHERE `filter`.`dataset`='" . addslashes($alias) . "'";

        $q .= " AND `filter`.`deleted`='0'";

        $q .= " ORDER BY `filter`.`ord` ASC 
        LIMIT 100";

        $result = $this->query($q);

        if (!is_null($exclude_alias)) {
            $exclude = $this->query("SELECT field FROM datasets_filters_datasets WHERE dataset = '$alias' and perspective = '$exclude_alias'");
            $excludefields = array();

            foreach ($exclude as $field)
                array_push($excludefields, $field['datasets_filters_datasets']['field']);

            foreach ($result as $key => $val)
                if (in_array($val['filter']['field'], $excludefields))
                    unset($result[$key]);
        }


        return $result;

    }

    public function getSwitchers($alias, $full = true, $exclude = null)
    {

        $fields = array('`name`', '`label`', '`dataset_search_default`');

        if ($full)
            $fields = array_merge($fields, array('`expression`'));

        return $this->query("SELECT " . implode(", ", $fields) . " 
	    	FROM `datasets_switchers` AS `switcher`
	    	WHERE `dataset`='" . addslashes($alias) . "'
	    	ORDER BY `ord` ASC 
	    	LIMIT 100");

    }
    
    public function getMap($alias, $page)
    {
		
		$dataset = $this->query("SELECT `id` FROM `datasets` WHERE `base_alias`='" . addslashes( $alias ) . "' LIMIT 1");
		$dataset_id = $dataset[0]['datasets']['id'];

        if (empty($page)) {
            $page = 1;
        }
        $page = (int) $page;
        if ($page < 1) {
            $page = 1;
        }
		$offset = ($page-1) * 50000;
			
        $items = $this->query("SELECT object_id 
	    	FROM `objects`
	    	WHERE `dataset_id`='" . $dataset_id . "'
	    	AND `a`='3'
	    	ORDER BY `object_id` DESC 
	    	LIMIT $offset, 50000");
	    	
	    $output = array();
	    foreach( $items as $item )
	    	$output[] = $item['objects']['object_id'];
	    
	    return $output;
	    
    }

    public function getSortings($alias)
    {

        return $this->query("SELECT `field`, `label`, `direction`
	    	FROM `datasets_orders` AS sorting
	    	WHERE `dataset`='" . addslashes($alias) . "'
	    	ORDER BY `ord` ASC 
	    	LIMIT 100");

    }

    public function getFields($alias)
    {

        return $this->query("SELECT `fields`.`alias`, `fields`.`field`, `fields`.`multiValue` 
	    	FROM `api_datasets_fields` AS `fields` 
	    	WHERE `fields`.`base_alias`='" . addslashes($alias) . "'
	    	LIMIT 100");

    }


    
    public function search($alias, $queryData = array()) {
	    
	    
		$dataset = $this->find('first', array(
			'conditions' => array(
			    'Dataset.alias' => $alias,
			),
			'full' => 1,
		));
		
		$virtual_fields = $dataset['virtual_fields'];		
		$filters = array(
			'dataset' => $alias,
		);
		$switchers = array();
		$facets = array();
		$order = array();
		$q = false;
					
		if( isset($queryData['conditions']) && is_array($queryData['conditions']) ) {
			foreach( $queryData['conditions'] as $key => $value ) {
			
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
		
		
		
		
		if (!empty($switchers)) {
                        
            $dataset_switchers_exp_dict = array_column($dataset['switchers'], 'switcher');
            $dataset_switchers_exp_dict = array_column($dataset_switchers_exp_dict, 'expression', 'name');
						
			foreach( $switchers as $key => $value ) {
                if( $exp = $dataset_switchers_exp_dict[ $key ] ) {
					
					// debug( $dataset_switchers_exp_dict ); die();
                    // $filters[] = $exp;

                }
            }
						
        }		
		
		
		$facets_dict = array();
		if( isset($dataset['filters']) ) {
				
			foreach( $dataset['filters'] as $filter ) 
				if( ( $filter = $filter['filter'] ) && in_array($filter['typ_id'], array(1, 2)) ) {
										
					$facets[] = array($filter['field'], in_array($filter['field'], $virtual_fields));
					$facets_dict[ $filter['field'] ] = $filter;
				
				}
		
		}
		
		
		if( isset($queryData['order']) && $queryData['order'] )
			$order = $queryData['order'];	
		
				
		
		App::import('model','Dane.Dataobject');
		$this->Dataobject = new Dataobject();
        $search = $this->Dataobject->find('all', array(
        	'q' => $q,
        	'filters' => $filters,
        	'facets' => $facets,
        	'order' => $order,
        	'limit' => (isset($queryData['limit']) && $queryData['limit']) ? $queryData['limit'] : 20,
        ));
		
		
		if( isset($search['facets']) ) {
						
			App::import('model', 'DB');
	        $this->DB = new DB();
			
			$facets = array();
			foreach( $search['facets'] as $field => $buckets ) {
				
				$filter = $facets_dict[ $field ];
				$buckets = $buckets[ 0 ];
				$options = array();
				
				if ($filter['typ_id'] == '1') {

		            $ids = array();
		            foreach ($buckets as $b)
		                if( $b['key'] && $b['doc_count'] )
		                    $ids[] = $b['key'];
		
		
		            if (isset($filter['params']['table'])) {
		
		                $id_field = isset($filter['params']['id_field']) ? $filter['params']['id_field'] : 'id';
		                $title_field = isset($filter['params']['title_field']) ? $filter['params']['title_field'] : 'nazwa';
		                $table = $filter['params']['table'];
		
		                $data = $this->DB->selectAssocs("SELECT `$id_field` as 'id', `$title_field` as 'label' FROM `$table` WHERE `$id_field`='" . implode("' OR `$id_field`='", $ids) . "'");
						$data = array_column( $data, 'label', 'id' );
						
						
						foreach( $buckets as $b )
							$options[] = array(
								'id' => $b['key'],
								'count' => $b['doc_count'],
								'label' => array_key_exists($b['key'], $data) ? $data[ $b['key'] ] : ' - ',
							);
							
		                $filter['params'] = array(
		                	'options' => $options,
		                );
		
		            }
		
		        } elseif ($filter['typ_id'] == '2') {
					
					$data = array_column($buckets, 'doc_count', 'key');
					
		            for ($i = 0; $i < count($filter['params']['options']); $i++)
		                $filter['params']['options'][$i]['count'] = @$data[ strtolower( $filter['params']['options'][$i]['id'] ) ];
		
		        }
		        
		        $facets[] = $filter;
				
			}
			
			$search['facets'] = $facets;
			
		}
				
		return $search;
	    
    }

}