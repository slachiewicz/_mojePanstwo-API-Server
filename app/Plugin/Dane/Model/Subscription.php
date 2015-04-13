<?

class Subscription extends AppModel
{
    
    public function afterSave($created, $options = array()) {
	    
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
								    'dataset' => $this->data['Subscription']['dataset'],
							    ),
						    ),
						    array(
							    'term' => array(
								    'id' => $this->data['Subscription']['object_id'],
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
			$params['id']    = $this->data['Subscription']['id'];
			$params['parent'] = $_id;
			
			$cts = strtotime( $this->data['Subscription']['cts'] );
			$mask = "Ymd\THis\Z";
			
			$params['body']  = array(
				'id' => $this->data['Subscription']['id'],
				'query' => array(
					'match' => array(
						'text' => 'test',
					),
				),
				'cts' => date($mask, $cts),
				'q' => $this->data['Subscription']['q'],
				'channel' => $this->data['Subscription']['channel'],
				'hash' => $this->data['Subscription']['hash'],
				'user_type' => $this->data['Subscription']['user_type'],
				'user_id' => $this->data['Subscription']['user_id'],
			);
			
			var_export( $params );
			$ret = $ES->API->index($params);		
			var_export( $ret );	    
		    die();
		    
		    debug('afterSave');
		    debug( $this->data );
		    
		    die();
		    
		}
	    
    }
    
    public function generateData($data = array()) {
	    
	    $base = '/dane';
	    $base .= '/' . $data['dataset'];
	    $base .= '/' . $data['object_id'];
		
		if( $data['dataset']=='prawo' )
			$base .= '/feed';
		
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

}


