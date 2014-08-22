<?
class MPSearch {

    public $cacheSources = true;
    public $description = 'Serwer szukania platformy mojePaństwo';

    private $_excluded_fields = array('datachannel', 'dataset', 'search', 'q');
    private $_fields_multi_dict = array();
    
    public function query(){
	    return null;
    }
    
    public function getSchemaName()
    {
        return null;
    }
	
    public function __construct($config)
    {

        require_once(APP . 'Vendor' . DS . 'autoload.php');
        $this->API = new Elasticsearch\Client(array(
	    	'hosts' => array(
	    		$config['host'] . ':' . $config['port'],
	    	),
	    ));
        // parent::__construct($config);

    }
    
    public function getObject($dataset, $id) {
	    
	    $params = array(
			'index' => 'objects*',
			'body' => array(
				'from' => 0, 
				'size' => 1,
				'query' => array(
					'filtered' => array(
				        'filter' => array(
				            'and' => array(
				                'filters' => array(
				                    array(
				                        'term' => array(
				                        	'_type' => $dataset,
				                        ),
				                    ),
				                    array(
				                    	'term' => array(
				                        	'id' => $id,
				                        ),
				                    ),
				                ),
				                '_cache' => true,
				            ),
				        ),
				    ),
				),
			),
		);

		
		// echo "\n\n"; debug( $params );
	    $es_result = $this->API->search($params);
	    // echo "\n\n"; debug( $es_result ); die();
	    
	    
	    $object = false;
	    if( $es_result && $es_result['hits']['total'] )
		    return $this->doc2object( $es_result['hits']['hits'][0] );
	    else 
	    	return false;
	    
	    
    }
    
    public function doc2object($doc) {
	    	    
	    $output = array(
    		'id' => $doc['_id'],
            'dataset' => $doc['_type'],
            'object_id' => $doc['_source']['id'],
            'data' => $doc['_source']['data'],
            'score' => $doc['_score'],
    	);
    	    	
    	if( isset($doc['highlight']['text']) && is_array($doc['highlight']['text']) && isset($doc['highlight']['text'][0]) )
    		$output['hl'] = $doc['highlight']['text'][0];
    	
    	return $output;
	    
    }

    private function getFieldType($field)
    {

        if (strpos($field, 'data') === 0)
            return 'date';
        elseif (strpos($field, 'date') === 0)
            return 'date';
        elseif (strpos($field, 'czas') === 0)
            return 'date';
        elseif (strpos($field, 'liczba') === 0)
            return 'int';
        elseif (in_array($field, array('rok', 'nr', 'numer', 'poz', 'pozycja', 'kolejnosc')))
            return 'int';

        return 'string';

    }
	
	
    public function read(Model $model, $queryData = array())
    {
		
        $params = array();
   
        App::import('model', 'MPCache');
        $this->MPCache = new MPCache();
    	$available_datasets = $this->MPCache->getAvailableDatasets();
    	
    	    	
    	
    	$queryLimit = (isset($queryData['limit']) && $queryData['limit']) ?
                    $queryData['limit'] :
                    20;
                    
        $queryLimit = max( min( $queryLimit, 100 ), 0 );
    	
    	$queryQ = (isset($queryData['q']) && $queryData['q']) ?
                    $queryData['q'] :
                    false;
    	
		$queryFilters = ( isset( $queryData['filters'] ) && is_array( $queryData['filters'] ) ) ? 
			$queryData['filters'] : 
			array();
			
		$queryFacets = ( isset( $queryData['facets'] ) && is_array( $queryData['facets'] ) ) ? 
			$queryData['facets'] : 
			array();
			
		$queryOrder = ( isset( $queryData['order'] ) && is_array( $queryData['order'] ) && isset($queryData['order'][0]) && is_array($queryData['order'][0]) ) ? 
			$queryData['order'][0] : 
			array();
			
			
        
        
        $and_filters = array();
        foreach( $queryFilters as $key => $value ) {
        	
        	$term = array();
        	
        	if( $key == 'dataset' ) {
        		
        		$_key = is_array($value) ? 'terms' : 'term';
        		$and_filters[] = array(
	        		$_key => array(
	        			'_type' => $value,
	        		),
	        	);
        		
        	} elseif( $key == '_source' ) {
        	
        		$src = $value[0];
        		if( $src ) {
	        		
	        		include( __DIR__ . '/MPSearchSources.php' );
	        		
        		}
        		
        	} else {
        		
        		$_key = is_array($value[0]) ? 'terms' : 'term';
        		$prefix = 'data';
        		if( $value[1] )
        			$prefix = 'data_virtual';
	        	
	        	$term[ $prefix . '.' . $key ] = $value[0];
	        	
	        	$and_filters[] = array(
	        		$_key => $term,
	        	);
	        	        	
        	}
        	
        	
        
        }
        
        $filtered = array(
	        'filter' => array(
	            'and' => array(
	                'filters' => $and_filters,
	                '_cache' => true,
	            ),
	        ),
	    );
	    
	    
	    
	    
        
        $params = array(
			'index' => 'objects*',
			'body' => array(
				'from' => 0, 
				'size' => $queryLimit,
				'query' => array(
					'filtered' => $filtered,
				),
			),
		);
		
		
		
		$sort = array();
		
		if( $queryQ ) {
			
			$params['body']['query']['filtered']['query']['match'] = array(
				'text' => $queryQ,
	    	);
			
			$sort[] = array(
				'_score' => 'desc',
			);
			
			$params['body']['highlight'] = array(
	    		'fields' => array(
	    			'text' => array(
	    				'index_options' => 'offsets',
	    				'number_of_fragments' => 1,
	    			),
	    		),
	    	);	    	
	    		
	    }
	    
	    
	    
	    foreach( $queryOrder as $order ) {
		    
		    $parts = explode(' ', $order);
		    $field = isset($parts[0]) ? $parts[0] : false;
		    
		    if( $field ) {				
				
				$direction = ( isset($parts[1]) && in_array($parts[1], array('asc', 'desc')) ) ? $parts[1] : 'asc';
				
				if( $field == '_title' ) {
					
					$sort[] = array(
						'title.raw' => $direction,
					);
				
				} elseif( $field == 'score' ) {
				
				
				
				} else {
					
					/*
					$field = 'data.' . $field;					
					$sort[] = array(
						$field => $direction,
					);
					*/
				
				}
				
			}
		    
	    }
        
        
        
        if( !empty($sort) )
	        $params['body']['sort'] = $sort;
        
        
        
        
        if( !empty($queryFacets) ) {
	        
	        $aggs = array();
	        
	        foreach( $queryFacets as $facet ) {
	        	
	        	if( is_array($facet) ) {
	        	
		        	$prefix = 'data';
		        	if( $facet[1] )
		        		$prefix = 'data_virtual';
		        	
		        	$aggs[ $facet[0] ] = array(
		        		'terms' => array(
		        			'field' => $prefix . '.' . $facet[0],
		        			'exclude' => '0',
		        			'size' => 20,
		        		),
		        	);
	        	
	        	} elseif( $facet == 'dataset' ) {
		        	
		        	$aggs[ 'dataset' ] = array(
		        		'terms' => array(
		        			'field' => '_type',
		        			'size' => 20,
		        		),
		        	);
		        	
	        	}
	        }
	        
	        $params['body']['aggs'] = $aggs;
	        
        }
        
        
        
  

        
        
        // echo "\n\n"; debug( $params );
	    $es_result = $this->API->search( $params );
	    // echo "\n\n"; debug( $es_result ); die();
        
        
        
        

        
        
        $output = array(
        	'pagination' => array(
        		'total' => null,
        	),
        	'dataobjects' => array(),
        );
        
        
	    if( $es_result && $es_result['hits']['total'] ) {
		    
		    $output['took'] = $es_result['took'];
		    
		    $output['pagination'] = array(
		    	'total' => $es_result['hits']['total'],
		    );
		    
		    foreach( $es_result['hits']['hits'] as $doc )
		    	$output['dataobjects'][] = $this->doc2object( $doc );
		    
	    }
	    
	    
	    if( !empty( $queryFacets ) ) 		    
		    foreach( $queryFacets as $field ) {
		    	
		    	if( is_array($field) )
		    		$field = $field[0];
		    	
			    if( isset( $es_result['aggregations'][$field] ) ) 
				    $output['facets'][$field][] = $es_result['aggregations'][$field]['buckets'];
				    
			}
        
        
        return $output;

    }



}