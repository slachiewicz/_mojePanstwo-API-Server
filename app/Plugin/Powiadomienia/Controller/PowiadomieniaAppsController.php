<?

class PowiadomieniaAppsController extends AppController
{
	
    public function index() {
	    
	    $apps = $this->PowiadomieniaApp->index();
	    
	    $this->set('apps', $apps);
	    $this->set('_serialize', 'apps');
	    
    }
} 