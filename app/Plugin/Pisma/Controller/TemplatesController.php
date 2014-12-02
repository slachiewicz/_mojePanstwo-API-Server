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
        $object = $this->Template->findById($this->request->params['id'], array(
	        'fields' => 'Template.id,Template.nazwa,Template.tresc,Template.adresat_opis'
        )); //$this->Dataobject->getObject('pisma_documents', $this->request->params['id']);

        if (!isset($object['Template']) || empty($object['Template'])) {
            throw new NotFoundException();
        }

        $this->setSerialized('object', $object['Template']);
    }
    
    public function grouped() {
        
        App::import('model', 'DB');
        $DB = new DB();
        
        $temp = $DB->selectAssocs("
        	SELECT `pisma_szablony`.`id`, `pisma_szablony`.`nazwa`, `pisma_szablony`.`opis`, `pisma_kategorie`.`id` as `kategoria_id`, `pisma_kategorie`.`nazwa` as `kategoria_nazwa`
			FROM `pisma_szablony` 
			JOIN `pisma_kategorie`
			ON `pisma_szablony`.`pisma_kategorie_id` = `pisma_kategorie`.`id` 
			WHERE pisma_szablony.akcept='1' 
			ORDER BY pisma_kategorie.ord ASC, pisma_szablony.ord ASC
		");
		
		$data = array();
		foreach( $temp as $t ) {
			
			$data[ $t['kategoria_id'] ]['id'] = $t['kategoria_id'];
			$data[ $t['kategoria_id'] ]['nazwa'] = $t['kategoria_nazwa'];		
			$data[ $t['kategoria_id'] ]['templates'][] = array(
				'id' => $t['id'],
				'nazwa' => $t['nazwa'],
				'opis' => $t['opis'],
			);
			
		}
		unset( $temp );
		$data = array_values($data);

        $this->setSerialized('data', $data);
    }
}