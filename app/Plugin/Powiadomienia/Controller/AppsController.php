<?

class AppsController extends AppController
{

	public $uses = array('Application');
	
    public function index() {
	    
	    $apps = $this->Application->find('all', array(
	    	'conditions' => array(
	    		'Application.alerts' => '1',
	    	),
	    ));
	    
	    $this->set('apps', $apps);
	    $this->set('_serialize', 'apps');
	    
    }
} 