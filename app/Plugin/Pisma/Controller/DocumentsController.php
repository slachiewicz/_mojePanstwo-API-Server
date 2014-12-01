<?

class DocumentsController extends AppController
{
    public $uses = array('Dane.Dataobject', 'Pisma.Document');
    public $components = array('Session');
	
	private function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
	}
	
	public function generateID($length = 5)
	{
		
	    $id = "";
	    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	    for($i=0;$i<$length;$i++)
	        $id .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
	    return $id;
			
	}
	
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

    public function search() {
        
        $user = $this->user;

		$search = $this->Document->search($user['id']);
		
        $this->setSerialized('search', $search);
    }

    public function view() {
        $object = $this->readOrThrow($this->request->params['id']);

        $this->setSerialized('object', $object);
    }

    public function save($id = null) {
        
        
        $map = array(
        	'id' => 'id', 
        	'data_pisma' => 'date',
        	'nazwa' => 'name',
        	'tytul' => 'title',
        	'tresc' => 'content',
        	'adresat' => 'to_str',
        	'nadawca' => 'from_str',
        	'miejscowosc' => 'from_location',
        	'data' => 'date',
        	'adresat_id' => 'to_id',
        	'szablon_id' => 'template_id',
        	'podpis' => 'from_signature',
        );
        
        $data = $this->request->data;
        if (empty($data)) {
            $data = array();
        }
        
        $temp = array();
        foreach( $data as $k => $v )
        	if( array_key_exists($k, $map) )
        		$temp[ $map[$k] ] = $v;
        
        $data = $temp;
        unset( $temp );
        
        
        // edit & create in one func, path param has precedence
        if ($id != null) {
            $data['id'] = $id;
        }

        

        if (isset($data['id']) && $data['id']) {
            $data = array_merge($this->readOrThrow($data['id']), $data);

        } else {
            $this->Document->create();
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['hash'] = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
            $data['alphaid'] = $this->generateID(5);
            
            $data['from_user_id'] = $this->user_id;
	        $data['from_name'] = "from_name";
	        $data['from_email'] = "test@test.com";
	        $data['to_dataset'] = 'instytucje';	        
	        $data['slug'] = substr(Inflector::slug($data['name'], '-'), 0, 127);
	        	        
        }

        
        
        
        // TODO powinno być zwrócone w innym formacie dTt, czemu cake sam tego nie formatuje w bazie?! Albo zwracajac?
        $data['modified_at'] = date('Y-m-d H:i:s');

        if ($doc = $this->Document->save(array('Document' => $data))) {
            $this->response->statusCode(201);  // 201 Created
            $this->setSerialized('object', array(
	            'id' => $doc['Document']['alphaid'],
	            'url' => '/pisma/' . $doc['Document']['alphaid'] . ',' . $doc['Document']['slug'],
            ));

        } else {
            // TODO format returned validation errors
            throw new ValidationException($this->Document->validationErrors);
        }
    }

    public function send() {
        //  TODO
    }

    public function delete() {
        $this->readOrThrow($this->request->params['id']);

        $this->Document->delete($this->request->params['id']);

        $this->response->statusCode(204);  // 204 No content
        $this->setSerialized(array());
    }

    private function readOrThrow($id) {
	    
        $object = $this->Document->findByAlphaid($id);

        if (!isset($object['Document']) || empty($object['Document'])) {
            throw new NotFoundException();
        }
        if ($object['Document']['from_user_id'] != $this->user_id) {
            throw new ForbiddenException();
        }

        return $object['Document'];
    }
}