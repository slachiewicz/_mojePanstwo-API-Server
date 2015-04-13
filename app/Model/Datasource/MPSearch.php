<?
class MPSearch {

    public $cacheSources = true;
    public $description = 'Serwer wyszukania platformy mojePaństwo';
	public $API;
	
	private $_index = 'mojepanstwo_v1';
	private $_data_prefix = 'data';
    private $_excluded_fields = array('datachannel', 'dataset', 'search', 'q');
    private $_fields_multi_dict = array();
    
    public $lastReponse = false;
    
    private $aggs_allowed = array(
	    'date_histogram' => array('field', 'interval', 'format'),
	    'terms' => array('field', 'include', 'exclude', 'size'),
	    'range' => array('field', 'ranges'),
	    'sum' => array('field'),
	    'nested' => array('path'),
	    'aggs' => array(),
	    'global' => array(),
    );
    
    public $Aggs = array();
    
    public function query(){
	    return null;
    }
    
    public function getSchemaName()
    {
        return null;
    }
	
	public function getLastReponse($field = '*')
	{
		
		if( $field == '*' )
			return $this->lastReponse;
		
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
    
    /*
    public function getCurrentUser($field = false) {
	 	
		App::uses('CakeSession', 'Model/Datasource');
		$Session = new CakeSession();	
		$user = $Session->read('Auth.User');
				
		if( $user && is_array($user) ) {
						
			if( $field===false )
				return $user;
			else
				return isset( $user[$field] ) ? $user[$field] : false;
 			
		} else return false;
	
	}
    
    public function search($body) {
	    
	    $params = array(
	    	'index' => $this->_index,
	    	'body' => $body,
	    );
	    
	    return $this->API->search($params);
	    
    }
    
    public function getObject($dataset, $id, $field='id') {
	    
	    if( $field!='id' )
	    	$field = 'data.' . $field;
	    
	    $params = array(
			'index' => $this->_index,
			'type' => 'objects',
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
				                        	'dataset' => $dataset,
				                        ),
				                    ),
				                    array(
				                    	'term' => array(
				                        	$field => $id,
				                        ),
				                    ),
				                ),
				                '_cache' => true,
				            ),
				        ),
				    ),
				),
				'fields' => array('dataset', 'id', 'slug'),
				'partial_fields' => array(
					'source' => array(
						'include' => array('data'),
					),
				),
			),
		);

		
		// echo "\n\n"; var_export( $params );
	    $es_result = $this->API->search($params);
	    // echo "\n\n"; debug( $es_result ); die();
	    
	    
	    $object = false;
	    if( $es_result && $es_result['hits']['total'] )
		    return $this->doc2object( $es_result['hits']['hits'][0] );
	    else 
	    	return false;
	    
	    
    }
    */
    
    public function doc2object($doc) {
	    
	    // echo "\n\n"; debug( $doc );
	    
	    $output = array(
            'global_id' => $doc['_id'],
            'dataset' => $doc['fields']['dataset'][0],
    		'id' => $doc['fields']['id'][0],
    		'slug' => $doc['fields']['slug'][0],
            'score' => $doc['_score'],
            'data' => $doc['fields']['source'][0]['data'],     
    	);
    	
    	
    	if( 
	    	isset( $doc['fields']['source'][0]['static'] ) && 
	    	!empty( $doc['fields']['source'][0]['static'] )
    	) {
	    	
			$output['static'] = $doc['fields']['source'][0]['static'];
	    	
	    }
    	
    	if( 
	    	isset( $doc['fields']['source'][0]['contexts'] ) && 
	    	!empty( $doc['fields']['source'][0]['contexts'] )
    	) {
	    	
	    	$context = array();
    		foreach( $doc['fields']['source'][0]['contexts'] as $key => $value ) {
	    		
	    		$key_parts = explode('.', $key);
	    		$value_parts = explode("\n\r", $value);
	    		
	    		$context[] = array(
		    		'creator' => array(
			    		'dataset' => $key_parts[0],
			    		'id' => $key_parts[1],
			    		'global_id' => $value_parts[0],
			    		'name' => $value_parts[1],
			    		'slug' => $value_parts[2],
			    		'url' => @$value_parts[5],
		    		),
		    		'action' => $key_parts[2],
		    		'label' => $value_parts[3],
		    		'sentence' => $value_parts[4],
	    		);
	    		
    		}
    		$output['contexts'] = $context;
    	
    	}
    	
    	if( 
    		isset( $doc['highlight']['text'] ) && 
    		is_array( $doc['highlight']['text'] ) && 
    		isset( $doc['highlight']['text'][0] )
    	)
    		$output['highlight'] = array($doc['highlight']['text']);
    	
    	return $output;
	    
    }	
	
    public function read(Model $model, $queryData = array())
    {
		
		// Configure::write('debug', 2);
        // if( $this->getCurrentUser('id')=='2375' ) { echo "\n\n"; debug( 'asd' ); }
		
		
		/*
		array(
			'conditions' => array(
				'dataset' => 'prawo',
				'id' => '137911'
			),
			'fields' => null,
			'joins' => array(),
			'limit' => (int) 1,
			'offset' => null,
			'order' => array(
				(int) 0 => null
			),
			'page' => (int) 1,
			'group' => null,
			'callbacks' => '1',
			'layers' => array(
				(int) 0 => 'docs',
				(int) 1 => 'counters',
				(int) 2 => 'files',
				(int) 3 => 'tags',
				(int) 4 => 'dataset'
			),
			'apiKey' => '1234abcd'
		)
		*/
		
		
		$this->lastReponse = false;
		
		if( !isset($queryData['conditions']) )
			$queryData['conditions'] = array();
		
		
		$from = ( $queryData['page'] - 1 ) * $queryData['limit'];
		$size = $queryData['limit'];
		
		$params = array(
			'index' => $this->_index,
			'type' => 'objects',
			'body' => array(
				'from' => $from, 
				'size' => $size,
				'query' => array(
					'function_score' => array(
		        		'query' => array(),
		        		'field_value_factor' => array(
							'field' => 'weights.main.score'
				        ),
		        	),
				),
				'fields' => array('dataset', 'id', 'slug'),
				'partial_fields' => array(
					'source' => array(
						'include' => array('data', 'static'),
					),
				),
				'sort' => array(
					array(
						'date' => 'desc',
					),
					array(
						'title' => 'asc',
					)
				),
			),
		);
		
		// debug($queryData); die();
		
		if( isset($queryData['order']) && is_array($queryData['order']) ) {
			
			$sort = array();
			foreach( $queryData['order'] as $os) {
				if( is_array($os) ) {
					foreach( $os as $o ) {
						
						$parts = explode(' ', $o);
						$partsCount = count( $parts );
						
						$field = false;
						$direction = 'desc';
						
						if( $partsCount===1 )
							$field = $o;
						elseif( $partsCount===2 )
							list($field, $direction) = $parts;
						
						if( $field ) {
							
							if( $field=='weight' ) {
								
								$field = 'weights.main.score';
								
							} elseif( $field=='_title' ) {
								
								$field = 'title.raw';
								
							}
							
							$sort[] = array(
								$field => $direction,
							);
							
						}
						
					}
				}
			}
			
			if( !empty($sort) )
				$params['body']['sort'] = $sort;
			
		}
		
		if( isset( $queryData['aggs'] ) ) {
			
			// debug( $queryData['aggs'] );
			$aggs = array();
						
			foreach( $queryData['aggs'] as $agg_id => $agg_data ) {
				
				if( 
					( $agg_id === '_channels' ) && 
					isset( $queryData['conditions']['_feed'] )
				) {
														
					$aggs['_channels'] = array(
	                    'global' => new \stdClass(),
	                    'aggs' => array(
	                        'feed_data' => array(
	                            'nested' => array(
	                                'path' => 'feeds_channels',
	                            ),
	                            'aggs' => array(
	                                'feed' => array(
	                                    'filter' => array(
	                                        'and' => array(
	                                            'filters' => array(
	                                                array(
	                                                    'term' => array(
	                                                        'feeds_channels.dataset' => $queryData['conditions']['_feed']['dataset'],
	                                                    ),
	                                                ),
	                                                array(
	                                                    'term' => array(
	                                                        'feeds_channels.object_id' => $queryData['conditions']['_feed']['object_id'],
	                                                    ),
	                                                )
	                                            ),
	                                        ),
	                                    ),
	                                    'aggs' => array(
	                                        'channel' => array(
	                                            'terms' => array(
	                                                'field' => 'feeds_channels.channel',
	                                                'size' => 100,
	                                            ),
	                                        ),
	                                    ),
	                                ),
	                            ),
	                        ),
	                    ),
	                );
	                
	                $this->Aggs[ '_channels' ][ 'global' ] = array();
					
				} else {
				
					foreach( $agg_data as $agg_type => $agg_params ) {
						
						if( in_array($agg_type, array_keys($this->aggs_allowed)) ) {
							
							$this->Aggs[ $agg_id ][ $agg_type ] = $agg_params;
							$es_params = array();
							
							foreach( $agg_params as $key => $value ) {
								if( ($agg_type == 'aggs') || in_array($key, $this->aggs_allowed[$agg_type]) ) {
									
									if( 
										( $key == 'field' ) && 
										!in_array($value, array('date', 'dataset'))
									) {
										$value = 'data.' . $value;
									}
									
									$es_params[ $key ] = $value;
								
								}
							}
													
							if( !empty($es_params) )
								$aggs[ $agg_id ][ $agg_type ] = $es_params;
						
						}
						
					}
				
				}
			}
							
			if( !empty($aggs) )
				$params['body']['aggs'] = $aggs;
			
			// debug($aggs); die();
			
		}
		
		
		// FITERS
		
		$and_filters = array();
        
                    
        foreach( $queryData['conditions'] as $key => $value ) {
        	      
        	if( in_array($key, array('dataset', 'id')) ) {
        		
        		$filter_type = is_array($value) ? 'terms' : 'term';
        		$and_filters[] = array(
	        		$filter_type => array(
	        			$key => $value,
	        		),
	        	);
        	        		        		
        	} elseif( $key == 'q' ) {
				
				if( $value ) {
					
					$params['body']['query']['function_score']['query']['filtered']['query']['match_phrase']['text'] = array(
			        	'query' => $value,
						'analyzer' => 'pl',
						'slop' => 3,
		        	);
		        	
		        	unset( $params['body']['sort'] );
	        	
	        	}
        	
        	} elseif( $key == '_main' ) {
        	
        		$and_filters[] = array(
	        		'term' => array(
	        			'weights.main.enabled' => true,
	        		),
	        	);
        	
        	} elseif( $key == 'feeds_channels' ) {
        		        		
        		$and_filters[] = array(
	        		'nested' => array(
	        			'path' => 'feeds_channels',
	        			'filter' => array(
		        			'and' => arraY(
			        			'filters' => array(
				        			array(
					        			'term' => array(
						        			'feeds_channels.dataset' => $value['dataset'],
					        			)
				        			),
				        			array(
					        			'term' => array(
						        			'feeds_channels.object_id' => $value['object_id'],
					        			)
				        			),
				        			array(
					        			'term' => array(
						        			'feeds_channels.channel' => $value['channel'],
					        			)
				        			),
			        			),
		        			),
	        			),
        			),
	        	);
	        	        	        	
        	} elseif( $key == '_feed' ) {
        		
        		if (
        			isset($value['dataset']) && 
        			isset($value['object_id']) && 
        			is_numeric($value['object_id'])
        		) {
	        		
	        		$_and_filters = array(
	        			array(
	        				'term' => array(
		        				'feeds_channels.dataset' => $value['dataset'],
		        			),
	        			),
	        			array(
	        				'term' => array(
		        				'feeds_channels.object_id' => (int) $value['object_id'],
		        			),
	        			),
        			);
        			
        			if (
	        			isset($value['channel']) && 
	        			is_numeric($value['channel'])
	        		) 
	        			$_and_filters[] = array(
		        			'term' => array(
			        			'feeds_channels.channel' => $value['channel'],
		        			),
	        			);
	        		
        			$and_filters[] = array(
	        			'nested' => array(
		        			'path' => 'feeds_channels',
		        			'filter' => array(
			        			'and' => arraY(
				        			'filters' => $_and_filters,
			        			),
		        			),
	        			),
        			);
	        		
		        	$params['body']['partial_fields']['source']['include'][] = 'contexts.' . $value['dataset'] . '.' . $value['object_id'] . '.*';
	        			        			
        		}	        	
	        	
	        } elseif( in_array($key, array('date', '_date')) ) {
	        		        		        	
	        	$_value = strtoupper($value);	        	
	        	
	        	$key = 'date';
	        	
	        	if( $_value == 'LAST_24H' ) {   		
						
					$range = array(
						'gte' => 'now-1d',
					);
					
					$and_filters[] = array(
						'range' => array(
							'date' => $range,
						),
					);
				
				} elseif( $_value == 'LAST_1D' ) {   		
					
					$range = array(
						'gte' => 'now-1d',
					);
					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
				
				} elseif( $_value == 'LAST_3D' ) {   		
					
					$range = array(
						'gte' => 'now-3d',
					);
					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
				
				} elseif( $_value == 'LAST_7D' ) {   		
					
					$range = array(
						'gte' => 'now-7d',
					);
					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
				
				} elseif( $_value == 'LAST_1M' ) {   		
					
					$range = array(
						'gte' => 'now-1M',
					);
					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
				
				} elseif( $_value == 'LAST_1Y' ) {   		
					
					$range = array(
						'gte' => 'now-1Y',
					);
					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
				
				} elseif( preg_match('^\[(.*?) TO (.*?)\]^i', $_value, $match) ) {
					
					$range = array();
					
					if( ($gte = $this->formatDate( $match[1] )) && ($gte != '*') )
						$range['gte'] = strtolower( $gte );
					if( ($lte = $this->formatDate( $match[2] )) && ($lte != '*') )
						$range['lte'] = strtolower( $lte );

					
					$and_filters[] = array(
						'range' => array(
							$key => $range,
						),
					);
					
				} else {
					
					$and_filters[] = array(
		        		'term' => array(
		        			$key => $value,
		        		),
		        	);
		        	
		        }
        	
        	} elseif( $key == 'subscribtions' ) {
	        	
	        	$and_filters[] = array(
	        		'has_child' => array(
	        			'type' => 'subs',
	        			'filter' => array(
		        			'and' => array(
			        			'filters' => array(
				        			array(
					        			'term' => array(
						        			'user_type' => $value['user_type'],
					        			),
				        			),
				        			array(
					        			'term' => array(
						        			'user_id' => $value['user_id'],
					        			),
				        			),
			        			),
		        			),
	        			),
	        		),
	        	);
        	
        	} else {
	        	
	        	$and_filters[] = array(
		        	'term' => array(
			        	'data.' . $key => $value,
		        	),
	        	);
	        	
        	}

        }
		

		// debug($params); die();
		$params['body']['query']['function_score']['query']['filtered']['filter']['and']['filters'] = $and_filters;
		
		
		
		
		if( 
			isset($queryData['highlight']) && 
			$queryData['highlight'] && 
			isset($queryData['conditions']) && 
			$queryData['conditions']
		) {
			
			$params['body']['highlight'] = array(
	    		'fields' => array(
	    			'text' => array(
	    				'index_options' => 'offsets',
	    				'number_of_fragments' => 1,
	    				'fragment_size' => 160,
	    			),
	    		),
	    		
	    	);
			
		}
				
						
		
		
		// debug( $params ); die();
		
		$response = $this->API->search( $params ); 

        $this->lastResponse = $response;
        if( isset($this->lastResponse['hits']) && isset($this->lastResponse['hits']['hits']) )
	        unset( $this->lastResponse['hits']['hits'] );
        
        // debug( $response['aggregations'] ); die();
        // debug( $this->Aggs );
        
        if( !empty($this->Aggs) ) {
	        
	        foreach( $this->Aggs as $agg_id => &$agg_data ) {
			    
			    
		        if( isset($response['aggregations'][$agg_id]) )
		        	$agg_data = $response['aggregations'][$agg_id];
			        			        
	        }
	        
	        unset( $this->lastResponse['aggregations'] );
	        
        }
                
        $hits = $response['hits']['hits'];
        for( $h=0; $h<count($hits); $h++ ) 
        	$hits[$h] = $this->doc2object( $hits[$h] );
        
        return $hits;        

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