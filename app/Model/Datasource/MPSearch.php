<?
class MPSearch {

    public $cacheSources = true;
    public $description = 'Serwer szukania platformy mojePaństwo';
	
	private $_index = 'objects_v1_01';
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
			'index' => $this->_index,
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
    	    	
    	if( isset($doc['highlight']['text_pl']) && is_array($doc['highlight']['text_pl']) && isset($doc['highlight']['text_pl'][0]) )
    		$output['hl'] = $doc['highlight']['text_pl'][0];
    	
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
    	
    	$queryPage = (isset($queryData['page']) && is_numeric($queryData['page'])) ?
                    $queryData['page'] :
                    1;
    	
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
		
		$queryObjects = ( isset( $queryData['objects'] ) && is_array( $queryData['objects'] ) ) ? 
			$queryData['objects'] : 
			array();
		
		if( empty($queryObjects) )
			$queryObjects = ( isset( $queryData['conditions']['objects'] ) && is_array( $queryData['conditions']['objects'] ) ) ? 
				$queryData['conditions']['objects'] : 
				array();
		
		
			
        
        
        $and_filters = array();
        foreach( $queryFilters as $key => $value ) {
        	
        	
        	
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
        		
			} elseif( $key == 'page' ) {
			
				// ignore this key
        	
        	} elseif( $key == 'limit' ) {

				// ignore this key
        		
        	} else {
        		
        		$prefix = 'data';
        		if( $value[1] )
        			$prefix = 'data_virtual';
	        	
	        	$key = $prefix . '.' . $key;
        		

				if( is_string($value[0]) && preg_match('^\[(.*?) TO (.*?)\]^i', $value[0], $match) ) {
					
					$range = array();
					
					if( $gte = $this->formatDate( $match[1] ) )
						$range['gte'] = $gte;
					if( $lte = $this->formatDate( $match[2] ) )
						$range['lte'] = $lte;

					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
					
					
				} else {
					
					$_key = is_array($value[0]) ? 'terms' : 'term';
		        	
		        	$and_filters[] = array(
		        		$_key => array(
		        			$key => $value[0],
		        		),
		        	);
	        	
	        	}
	        	        	
        	}
        	
        	
        
        }
        
        if( !empty( $queryObjects ) ) {
        	
        	$ors = array();
        	
	        foreach( $queryObjects as $obj ) {
		        
		        $ors[] = array(
        			'and' => array(
        				array(
        					'term' => array(
				        		'_type' => $obj['dataset'],
		        			),
        				),
        				array(
        					'term' => array(
				        		'id' => $obj['object_id'],
		        			),
        				),
        			),
        		);
		        
	        }
	        
	        $and_filters[] = array(
	        	'or' => $ors,
	        );
        
        }
        
        
        $filtered = array(
	        'filter' => array(
	            'and' => array(
	                'filters' => $and_filters,
	                '_cache' => true,
	            ),
	        ),
	    );
	    
	    
	    
	    $_from = ( $queryPage - 1 ) * $queryLimit;
	    
        $params = array(
			'index' => $this->_index,
			'body' => array(
				'from' => $_from, 
				'size' => $queryLimit,
				'query' => array(
					'filtered' => $filtered,
				),
			),
		);
		
		
		
		$sort = array();
		
		if( $queryQ ) {
			
			$params['body']['query']['filtered']['query']['match_phrase'] = array(
				'text_pl' => array(
					'query' => $queryQ,
					'analyzer' => 'morfologik',
					'slop' => 10,
				),
	    	);
			
			$sort[] = array(
				'_score' => 'desc',
			);
			
			$params['body']['highlight'] = array(
	    		'fields' => array(
	    			'text_pl' => array(
	    				'index_options' => 'offsets',
	    				'number_of_fragments' => 1,
	    			),
	    		),
	    		
	    	);
	    	
	    	$params['body']['suggest'] = array(
			    "text" => $queryQ,
			    "didyoumean" => array(
			      "phrase" => array(
			        "analyzer" => "simple",
			        "field" => "text_pl",
			        "size" => 1,
			        "real_word_error_likelihood" => 0.95,
			        "max_errors" => 0.5,
			        "gram_size" => 2,
			        "direct_generator" => array(
			        	array(
				          "field" => "text_pl",
				          "suggest_mode" => "always",
				          "min_word_length" => 1
				        ),
				    ),
			        "highlight" => array(
			          "pre_tag" => "<em>",
			          "post_tag" => "</em>"
			        )
			      )
			    )
			); 	
	    		
	    }
	    
	    
	    
	    foreach( $queryOrder as $order ) {
		    
		    $parts = explode(' ', $order);
		    $field = isset($parts[0]) ? $parts[0] : false;
		    
		    if( $field ) {				
				
				$direction = ( isset($parts[1]) && in_array($parts[1], array('asc', 'desc')) ) ? $parts[1] : 'asc';
				
				if( $field == '_title_pl' ) {
					
					$sort[] = array(
						'title_pl.raw' => $direction,
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
        		'count' => null,
        		'total' => null,
        		'from' => null,
        		'to' => null,
        	),
        	'performance' => array(
        		'took' => null,
        	),
        	'dataobjects' => array(),
        	'didyoumean' => false,
        );
        
        
        if( $es_result ) {
        	
        	$_count = count( $es_result['hits']['hits'] );
        	$output['performance']['took'] = $es_result['took'];
        	
        	$output['pagination'] = array_merge($output['pagination'], array(
        		'count' => $_count,
        		'total' => $es_result['hits']['total'],
        		'from' => $_from + 1,
        		'to' => $_from + $_count,
        	));
        	
		    if( $_count ) {
			    
			    foreach( $es_result['hits']['hits'] as $doc )
			    	$output['dataobjects'][] = $this->doc2object( $doc );
			    
		    } else {
		    
			    if( isset($es_result['suggest']) && isset($es_result['suggest']['didyoumean']) && !empty($es_result['suggest']['didyoumean']) ) {
				    
				    $didyoumean = @array_shift( $es_result['suggest']['didyoumean'] );
				    
				    if( isset($didyoumean['options']) && !empty($didyoumean['options']) )
				    	$output['didyoumean'] = $didyoumean['options'][0]['highlighted'];
				    
			    }
		    
		    }
	    
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

	private function formatDate( $inp ) {
		
		if( $inp == '*' ) {
			return false;
		} elseif( in_array($inp, array('NOW/DAY')) ) {
			return 'now';
		} else {
			return $inp;
		}
		
	}

}