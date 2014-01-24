<?

class ApplicationsController extends AppController
{

    public function index()
    {

        $this->set('applications', $this->Application->find('all'));
        $this->set('_serialize', array('applications'));

    }

}