<?

class DocumentsController extends AppController
{
    public $uses = array('Dane.Dataobject', 'Pisma.Document');
    public $components = array('Session');
	
	protected $user = false;
	
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
		
		if( $user = $this->Auth->user() ) {
			
			$this->user = array(
				'type' => 'account',
				'id' => $user['id'],
			);
			
		} elseif( isset($this->request->data['anonymous_user_id']) ) {
			
			$this->user = array(
				'type' => 'anonymous',
				'id' => $this->request->data['anonymous_user_id'],
			);
						
		} elseif( isset($this->request->query['anonymous_user_id']) ) {
			
			$this->user = array(
				'type' => 'anonymous',
				'id' => $this->request->query['anonymous_user_id'],
			);
						
		}
		
		/*
        if (!MpUtils::is_trusted_client($_SERVER['REMOTE_ADDR'])) {
            // deny access to Documents from untrusted clients
            throw new ForbiddenException();
        }

        if (empty($this->user)) {
            throw new UnauthorizedException();
        }
        */
    }

    public function search() {
                
        $params = array(
	        'page' => ( 
	        	isset($this->request->query['page']) && 
	        	is_numeric($this->request->query['page'])
	        ) ? $this->request->query['page'] : 1,
	        'q' => (isset( $this->request->query['q'] ) && $this->request->query['q']) ? $this->request->query['q'] : false,
	        'user_type' => $this->user['type'],
	        'user_id' => $this->user['id'],
        );
						
		$search = $this->Document->search($params);
        $this->setSerialized('search', $search);
        
    }

    public function view() {
        
        $temp = $this->readOrThrow($this->request->params['id']);
    	$this->setSerialized('object', $temp);
        
        /*
        if(
	        isset( $this->request->query['temp'] ) && 
	        (boolean) $this->request->query['temp'] 
        ) {
        
        	$temp = $this->readOrThrow($this->request->params['id']);
        	$this->setSerialized('object', $temp);
        
        } elseif( $object = $this->Document->get($this->request->params['id']) ) {
        	        
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
	        	'szablon_id' => 'template_id',
	        	'podpis' => 'from_signature',
	        	'alphaid' => 'alphaid',
	        	'to_dataset' => 'to_dataset',
	        	'to_id' => 'to_id',
	        	'adresat_nazwa' => 'to_name',
	        	'hash' => 'hash',
	        	'slug' => 'slug',
	        	'adresat_id' => 'to_id',
	        	'from_user_id' => 'from_user_id',
	        	'from_user_type' => 'from_user_type',
	        	
	        );
	        
	        $temp = array();
	        foreach( $map as $k=>$v )
	        	if( array_key_exists($v, $object) )
	        		$temp[$k] = $object[$v];
        	
        	$this->setSerialized('object', $temp);
        	
        } else {
	        
	        throw new NotFoundException('Could not find that document');
	        
        }
        */
        
    }
	
	public function update($id, $slug = null) {
		
		$status = false;		
		if(
			isset( $this->request->query['name'] ) && 
			( $name = $this->request->query['name'] )
		) {
			
			$status = $this->Document->rename($id, array(
				'name' => $name,
				'user' => $this->user,
			));
			
		}
		
		$this->set('status', $status);
		$this->set('_serialize', 'status');	
		
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
        	'szablon_id' => 'template_id',
        	'podpis' => 'from_signature',
        );
        
        
        $data = $this->request->data;
        if (empty($data)) {
            $data = array();
        }
        
        $adresat_id = isset($data['adresat_id']) ? $data['adresat_id'] : false;
        
        $temp = array();
        foreach( $data as $k => $v )
        	if( array_key_exists($k, $map) )
        		$temp[ $map[$k] ] = $v;
        
        
        if( $user = $this->Auth->user() ) {
	        
	        $temp['from_user_type'] = 'account';
	        $temp['from_user_id'] = $user['id'];
	        
        } elseif( isset($data['anonymous_user_id']) ) {
	        
	        $temp['from_user_type'] = 'anonymous';
	        $temp['from_user_id'] = $data['anonymous_user_id'];
	        
        } else return false;
        
        
        $data = $temp;
        unset( $temp );
        
        if(
	        $adresat_id && 
	        ( $parts = explode(':', $adresat_id) ) && 
	        ( count($parts)===2 )
        ) 
        	$data = array_merge($data, array(
	        	'to_dataset' => $parts[0],
	        	'to_id' => $parts[1],
        	));
        
        
        
        
        
        
        App::import('model','DB');
		$DB = new DB();
        
        
        // edit & create in one func, path param has precedence
        if ($id != null) {
            $data['alphaid'] = $id;
        }
				
        if (isset($data['alphaid']) && $data['alphaid']) {
            
            $data['modified_at'] = date('Y-m-d H:i:s');
            $data['saved'] = '1';
            $data['saved_at'] = date('Y-m-d H:i:s');

        } else {
	        
	        
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['hash'] = substr(bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)), 0, 64);
            $data['alphaid'] = $this->generateID(5);
	        $data['name'] = false;
	        $data['saved'] = '0';
	       
	    }
	    
	    
        if(
	        isset( $data['template_id'] ) && 
        	$data['template_id'] && 
        	$template = $DB->selectAssoc("SELECT nazwa, tresc FROM pisma_szablony WHERE id='" . addslashes( $data['template_id'] ) . "'")
        ) {
	        
        	$data['title'] = $template['nazwa'];
        	
        	if( $data['saved']=='0' ) {
	        	
	        	$data['content'] = $template['tresc'];
	        	
	        	if( !$data['name'] && $template['nazwa'] )
	        		$data['name'] = $template['nazwa'];
        		
        	}
        	
        }
        
        
        if(
	        isset( $data['to_dataset'] ) && 
        	$data['to_dataset'] && 
        	isset( $data['to_id'] ) && 
        	$data['to_id'] && 
        	( $data['to_dataset']=='instytucje' ) && 
        	( $to = $DB->selectAssoc("SELECT id, nazwa, email, adres_str FROM administracja_publiczna WHERE id='" . addslashes( $data['to_id'] ) . "'" ) )
        ) {
	       	
	       			       	 
        	$data['to_str'] = '<p>' . $to['nazwa'] . '</p><p>' . $to['adres_str'] . '</p>';
        	$data['to_name'] = $to['nazwa'];
        	$data['to_email'] = $to['email'];

        	
        }
	                
        	        	        	        

		if( !isset($data['to_dataset']) )
	        $data['to_dataset'] = false;	        
        
		if( !isset($data['to_id']) )        
			$data['to_id'] = false;	 
			
		$data['slug'] = @substr(Inflector::slug($data['name'], '-'), 0, 127);
                
        // TODO powinno być zwrócone w innym formacie dTt, czemu cake sam tego nie formatuje w bazie?! Albo zwracajac?
        $data['modified_at'] = date('Y-m-d H:i:s');
				
	    
	    /*
	    debug( $data );
	    $this->Document->save(array('Document' => $data));
	    $dbo = $this->Document->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);
		echo $lastLog['query']; die();   
		*/
	       
	    
	    if( $data['saved']=='0' ) {
	        
	        if( !$data['name'] )
	        	$data['name'] = 'Nowe pismo';
	        	
	        $this->Document->create();  
        
        } 
	    
	    	    
        if ($doc = $this->Document->save(array('Document' => $data))) {
            $this->response->statusCode(201);  // 201 Created
            
            $url = '/pisma/' . $doc['Document']['alphaid'];
            
            if( $doc['Document']['slug'] )
            	$url .= ',' . $doc['Document']['slug'];
            
            $this->setSerialized('object', array(
	            'id' => $doc['Document']['alphaid'],
	            'url' =>  $url,
            ));

        } else {
            // TODO format returned validation errors
            throw new ValidationException($this->Document->validationErrors);
        }
    }

    public function send($id = null) {
        
        if( $id ) {
	        
	        $status = $this->Document->send($id, $this->user, array());
	        $this->setSerialized('status', $status);
	        
        } else throw new BadRequestException();
        
    }

    public function delete() {
        
        $params = array();
        
        if( isset($this->request->query['anonymous_user_id']) ) {
        	$params['from_user_type'] = 'anonymous';
        	$params['from_user_id'] = $this->request->query['anonymous_user_id'];
        }
        
        $status = $this->Document->delete($this->request->params['id'], $params);
        $this->setSerialized('status', $status);
        
    }
	
	public function transfer_anonymous() {
		
		$status = false;
		
		if(
			( $user = $this->Auth->user() ) && 
			isset($this->request->query['anonymous_user_id']) && 
			$this->request->query['anonymous_user_id']
		) {
			
			$status = $this->Document->transfer_anonymous($this->request->query['anonymous_user_id'], $this->Auth->user('id'));
			
		}
		
		$this->setSerialized('status', $status);
		
	}
	
    private function readOrThrow($id) {
	    	    
        $object = $this->Document->find('first', array(
	        'conditions' => array(
		        'deleted' => '0',
		        'id' => $id,
		        'OR' => array(
			        array(
			        	'from_user_type' => $this->user['type'],
				        'from_user_id' => $this->user['id'],
				    ),
				    'access' => 'public',
			    )
	        ),
        ));
        
        /*
        $dbo = $this->Document->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);
		echo $lastLog['query']; die();
		*/
        
        if (!isset($object['Document']) || empty($object['Document'])) {
            throw new NotFoundException();
        }
        /*
        if ($object['Document']['from_user_id'] != $this->user_id) {
            throw new ForbiddenException();
        }
        */

        return $object['Document'];
    }
}