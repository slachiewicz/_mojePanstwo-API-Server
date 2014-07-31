<?

class TemplatesController extends AppController
{
    public $uses = array('Dane.Dataobject', 'Pisma.Template');

    public function index() {
        $conditions = array(
            'dataset' => array('pisma_templates'),
        );

        if (isset($this->request->query['q']) && !empty($this->request->query['q'])) {
            $conditions['q'] = $this->request->query['q'];
        }

        $data = $this->Dataobject->find('all', array(
            'conditions' => $conditions,
            'limit' => 10,
        ));

        $this->setSerialized('data', $data);
    }

    public function view() {
        $object = $this->Template->findById($this->request->params['id']); //$this->Dataobject->getObject('pisma_documents', $this->request->params['id']);

        if (!isset($object['Template']) || empty($object['Template'])) {
            throw new NotFoundException();
        }

        $this->setSerialized('object', $object['Template']);
    }
}