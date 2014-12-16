<?

App::uses('Dataobject', 'Dane.Model');

class PoslowieObject extends Dataobject
{
    
    public function getFeed($id, $params) {
	    
	    $filters = array(
		    '_source' => 'poslowie.aktywnosci:' . $id,
	    );
	    
	    $search = $this->find('all', array(
        	'q' => false,
        	'mode' => 'search_main',
        	'filters' => $filters,
        	'facets' => false,
        	'order' => false,
        	'limit' => 20,
        	'page' => 1,
        ));
        
        debug( $search );
	    
    }

}