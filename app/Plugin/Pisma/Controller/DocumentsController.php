<?

class DocumentsController extends AppController
{
    public $uses = array('Dane.Dataobject', 'Pisma.Document');
    public $components = array('Session');

    public function beforeFilter()
    {
        parent::beforeFilter();

        if (!MpUtils::is_trusted_client($_SERVER['REMOTE_ADDR'])) {
            // deny access to Documents from untrusted clients
            throw new ForbiddenException();
        }

        if (empty($this->user)) {
            throw new UnauthorizedException();
        }
    }

    public function index() {
        $user = $this->user;

        $response = $this->Document->find('all', array(
            'conditions' => array(
                'from_user_id' => $this->user_id
            )
        ));

        // TODO ustalić format zwracania obiektów przez index
        // - key: search
        // - opakowywanie w model_name (bo obiekty zalezne moga tez byc zwrocone)
        // - pagination czy przekazujemy w odpowiedzi?
        $this->setSerialized('data', $this->flatResponseArray($response, 'Document'));
    }

    public function view() {
        $object = $this->Document->read(null, $this->request->params['id']); //$this->Dataobject->getObject('pisma_documents', $this->request->params['id']);

        if (!isset($object['Document']) || empty($object['Document'])) {
            throw new NotFoundException();
        }

        if ($object['Document']['from_user_id'] != $this->user_id) {
            throw new ForbiddenException();
        }

        $this->setSerialized('object', $object['Document']);
    }

    public function save() {
        $data = $this->request->data;
        if (empty($data)) {
            $data = array();
        }

        if (isset($data['id']) && $data['id']) {
            $this->readOrThrow($data['id']);

        } else {
            $this->Document->create();
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        $data['from_user_id'] = $this->user_id;
        // TODO powinno być zwrócone w innym formacie dTt, czemu cake sam tego nie formatuje w bazie?! Albo zwracajac?
        $data['modified_at'] = date('Y-m-d H:i:s');

        if ($doc = $this->Document->save(array('Document' => $data))) {
            $this->response->statusCode(201);  // 201 Created
            $this->setSerialized('object', $doc['Document']);

        } else {
            // TODO format returned validation errors
            throw new ValidationException($this->Document->validationErrors);
        }
    }

    public function delete() {
        $this->readOrThrow($this->request->params['id']);

        $this->Document->delete();

        $this->response->statusCode(204);  // 201 No content
        $this->setSerialized(array());
    }

    private function readOrThrow($id) {
        $object = $this->Document->read(null, $id); //$this->Dataobject->getObject('pisma_documents', $this->request->params['id']);

        if (!isset($object['Document']) || empty($object['Document'])) {
            throw new NotFoundException();
        }
        if ($object['Document']['from_user_id'] != $this->user_id) {
            throw new ForbiddenException();
        }
    }
}