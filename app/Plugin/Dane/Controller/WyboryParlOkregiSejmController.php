<?

class WyboryParlOkregiSejmController extends AppController
{
    public $uses = array('Dane.Dataset', 'Dane.Dataobject');

    public function wskazniki() {	    
	    
	    $data = false;
	    if( 
	    	( $id = $this->request->params['id'] ) && 
	    	( $okreg_id = $this->request->params['object_id'] )
    	) {
		    
		    $this->loadModel('DB');
		    
		    if( $data = $this->DB->selectAssocs("SELECT rok, wartosc FROM bdl_data_okregi WHERE okreg_id='" . addslashes( $okreg_id ) . "' AND kombinacja_id='" . addslashes( $id ) . "' ORDER BY rok DESC") ) {
			    
			    foreach( $data as &$d ) {
				    
				    $d['rok'] = (int) $d['rok'];
				    $d['wartosc'] = (float) $d['wartosc'];
				    
			    }
			    
		    }
		    
		    
	    }
	    	    
	    $this->set('data', $data);
	    $this->set('_serialize', 'data');
	    
    }

}