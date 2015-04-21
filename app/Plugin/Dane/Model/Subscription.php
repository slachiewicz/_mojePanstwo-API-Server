<?

class Subscription extends AppModel
{
    
    public function index($data) {
	    	    
	    $ES = ConnectionManager::getDataSource('MPSearch');	    
	    
	    $parent_doc = $ES->API->search(array(
		    'index' => 'mojepanstwo_v1',
		    'type' => 'objects',
		    'body' => array(
			    'query' => array(
				    'bool' => array(
					    'must' => array(
						    array(
							    'term' => array(
								    'dataset' => $data['dataset'],
							    ),
						    ),
						    array(
							    'term' => array(
								    'id' => $data['object_id'],
							    ),
						    ),
					    ),
				    ),
			    ),
		    ),
	    ));
	    
	    if( 
	    	( $parent_doc['hits']['total'] === 1 ) && 
	    	( $_id = $parent_doc['hits']['hits'][0]['_id'] )
	    ) {
		    		    
		    $params = array();
			$params['index'] = 'mojepanstwo_v1';
			$params['type']  = '.percolator';
			$params['id']    = $data['id'];
			$params['parent'] = $_id;
			
			$cts = strtotime( $data['cts'] );
			$mask = "Ymd\THis\Z";
			
			
			
			if(
				isset( $data['conditions'] ) && 
				$data['conditions'] && 
				( $data['conditions'] = json_decode($data['conditions'], true) )
			) {
				$es_conditions = $data['conditions'];
			} else {
				$es_conditions = array();
			}
			
			$es_conditions['_feed'] = array (
				'dataset' => $data['dataset'],
				'object_id' => $data['object_id'],
			);
			
			if( isset($data['q']) && $data['q'] )
				$es_conditions['q'] = $data['q'];
				
			if( isset($data['channel']) && $data['channel'] )
				$es_conditions['_feed']['channel'] = $data['channel'];
				
			$es_query = $ES->buildESQuery(array(
				'conditions' => $es_conditions,
			));
			
			$params['body']  = array(
				'id' => $data['id'],
				'query' => $es_query['body']['query']['function_score']['query'],
				'cts' => date($mask, $cts),
				'hash' => $data['hash'],
				'user_type' => $data['user_type'],
				'user_id' => $data['user_id'],
				'url' => $data['url'],
				'title' => $data['title'],
			);
			
			if( isset($data['q']) && $data['q'] )
				$params['body']['q'] = $data['q'];
				
			if( isset($data['channel']) && $data['channel'] )
				$params['body']['channel'] = $data['channel'];
				
			
			$ret = $ES->API->index($params);	
			
			return $_id;	    
		    
		}
	    
    }
    
    public function afterDelete() {
		
		if( $this->data['id'] ) {
			
			$ES = ConnectionManager::getDataSource('MPSearch');
			$deleteParams = array();
			$deleteParams['index'] = 'mojepanstwo_v1';
			$deleteParams['type'] = '.percolator';
			$deleteParams['id'] = $this->data['id'];
			$ret = $ES->API->delete($deleteParams);
						
		}
	
	}
    
    public function generateData($data = array()) {
	    
	    $base = '/dane';
	    
	    if( $data['dataset']=='rady_gmin' ) {
		    
		    $base .= '/gminy/903,krakow/rada';
		    
	    } elseif( $data['dataset']=='urzedy_gmin' ) {

		    $base .= '/gminy/903,krakow/urzad';
	    
	    } else {
	    
		    $base .= '/' . $data['dataset'];
		    $base .= '/' . $data['object_id'];
			
			if( $data['dataset']=='prawo' )
				$base .= '/feed';
			
		}
		
		$title_parts = array();
		
	    $query = array('subscription' => $data['id']);
	    
	    if( isset($data['q']) && $data['q'] ) {
	    	$query['q'] = $data['q'];
	    	$title_parts[] = '"' . $query['q'] . '"';
	    }
	    	
	    if( 
	    	isset($data['channel']) && 
	    	$data['channel'] && 
	    	( $query['channel'] = $data['channel'] )
	    ) {
	    	
	    	App::import('model','Dane.DatasetChannel');
			$DatasetChannel = new DatasetChannel();
			if( $channel = $DatasetChannel->find('first', array(
				'fields' => array(
					'title'
				),
				'conditions' => array(
					'creator_dataset' => $data['dataset'],
					'channel' => $data['channel'],
				),
			)) ) {
				
				$title_parts[] = $channel['DatasetChannel']['title'];
				
			}
			
	    }
	    	
	    if( 
	    	isset($data['conditions']) && 
	    	is_string($data['conditions']) && 
	    	( $query['conditions'] = json_decode($data['conditions'], true) ) 
	    ) {
	    	$title_parts[] = 'Dodatkowe filtry';
	    }
	    	    
	    return array(
	    	'url' => $base . '?' . http_build_query($query),
	    	'title' => empty($title_parts) ? 'Wszystkie dane' : implode(' - ', $title_parts),
	    );
	    
    }
    
    public function transfer_anonymous($anonymous_user_id, $user_id) {
		
		if(
			( $db = ConnectionManager::getDataSource('default') ) && 
			( $where = "user_type='anonymous' AND user_id='" . addslashes( $anonymous_user_id ) . "'" ) && 
			( $subs = $db->query("SELECT id, parent_id FROM subscriptions WHERE $where") ) 
		) {
			
			$ES = ConnectionManager::getDataSource('MPSearch');
						
			foreach( $subs as $sub ) {
			    $ES->API->update(array(
				    'index' => 'mojepanstwo_v1',
				    'type' => '.percolator',
				    'id' => $sub['subscriptions']['id'],
				    'parent' => $sub['subscriptions']['parent_id'],
				    'body' => array(
					    'doc' => array(
						    'user_type' => 'account',
					    	'user_id' => $user_id,
					    ),
				    ),
			    ));
			}
			
			$db->query("UPDATE subscriptions SET `user_type`='account', `user_id`='" . addslashes( $user_id ) . "' WHERE $where");
			
			return true;
			
		} else return false;
		
	}

}


