<?

class ContactsController extends AppController
{
    public $uses = array('Dane.Dataobject');

    public function search() {
        $q = @$this->request->query['q'];
        if(empty($q)) {
            throw new BadRequestException('Query parameter is required: q');
        }

        $data = $this->Dataobject->find('all', array(
            'conditions' => array(
                'dataset' => array('krs_podmioty', 'administracja_publiczna'),
                'q' => $q . '* OR ' . $q,
            ),
            'limit' => 10,
        ));

        $this->setSerialized('data', $data);
    }
}