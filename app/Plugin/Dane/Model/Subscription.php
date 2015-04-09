<?

class Subscription extends AppModel
{
    
    public function generateData($data = array()) {
	    
	    $base = '/dane';
	    $base .= '/' . $data['dataset'];
	    $base .= '/' . $data['object_id'];

	    $query = array('subscription' => $data['id']);
	    
	    if( isset($data['q']) && $data['q'] )
	    	$query['q'] = $data['q'];
	    	
	    if( isset($data['channel']) && $data['channel'] )
	    	$query['channel'] = $data['channel'];
	    	
	    if( 
	    	isset($data['query']) && 
	    	is_string($data['query']) && 
	    	( $query = json_decode($data['query'], true) ) 
	    )
	    	$query['conditions'] = $query;
	    		    
	    return array(
	    	'url' => $base . '?' . http_build_query($query),
	    	'title' => 'Subskrypcja',
	    );
	    
    }

}


