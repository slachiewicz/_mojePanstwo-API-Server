<?php
App::uses('AppModel', 'Model');
/**
* PismaDocument Model
*
 * @property Paszport.User $User
 * @property Template $Template
*/
class Document extends AppModel {

    public $useTable = 'pisma_documents';
    public $primaryKey = 'alphaid';
    public $recursive = -1;
	
	public $virtualFields = array(
		'data_pisma' => 'Document.date',
	    'tytul' => 'Document.title',
	    'nazwa' => 'Document.name',
	    'tresc' => 'Document.content',
    	'nadawca' => 'Document.from_str',
    	'adresat' => 'Document.to_str',
    	'miejscowosc' => 'Document.from_location',
    	'data' => 'Document.date',
    	'adresat_id' => 'Document.to_id',
    	'szablon_id' => 'Document.template_id',
    	'podpis' => 'Document.from_signature',
    	'id' => 'Document.alphaid',
	);
	
	
        	
	
    /**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		/*
		'alphaid' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 5),
			),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
            ),
		),
		*/
		/*
		'hash' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 64),
			),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,

                //'message' => 'Your custom message here',
                //'last' => false, // Stop validation after this rule
                //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
		),
		'access' => array(
			'inList' => array(
				'rule' => array('inList', array('private', 'public')),
                'required' => true,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
		/*
		'from_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => false,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
		'from_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
                'required' => false,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
            'maxLength' => array(
                'rule' => array('maxLength', 255),
            ),
		),
		'from_email' => array(
            'email' => array(
                'rule' => 'email',
                'required' => false,
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 255),
            ),
		),
        'subject' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 255),
            ),
        ),
        /*
		'to_dataset' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
                'required' => false,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
		/*
		'to_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => false,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			)
		),
		*/
		'sent' => array(
			'boolean' => array(
				'rule' => array('boolean'),
                'required' => false,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

        //The Associations below have been created with all possible keys, those that are not needed can be removed
        
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Paszport.User',
			'foreignKey' => 'from_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Template' => array(
			'className' => 'Pisma.Template',
			'foreignKey' => 'template_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	
	public function afterSave($created, $options) {
				
		if( ($data = $this->data['Document']) && isset($data['alphaid']) && $data['alphaid'] ) {
			
			if( $saved = (isset($data['saved']) ? (boolean) $data['saved'] : false) ) {
				
				
				App::uses('ConnectionManager', 'Model');
				
				
				// GET HASH
				
				if(
					( $db = ConnectionManager::getDataSource('default') ) && 
					( $dbdata = $db->query("SELECT id, hash, to_name FROM pisma_documents WHERE alphaid='" . addslashes( $data['alphaid'] ) . "'") ) 
				) {		
					$data['id'] = $dbdata[0]['pisma_documents']['id'];
					$data['hash'] = $dbdata[0]['pisma_documents']['hash'];
					$data['to_name'] = $dbdata[0]['pisma_documents']['to_name'];
				}
									
				// SEND TO THUMBNAILS GENERATOR
				
				
				$db = ConnectionManager::getDataSource('MPCache');				
				$db->API->sadd('pisma/thumbnails/incoming', json_encode($data));				
				
				
				
				
				// SEND TO ELASTIC SEARCH
				
				$mask = "Ymd\THis\Z";
				
				$data['text'] = @$data['name'] . "\n";
				$data['text'] .= @$data['from_str'] . "\n";
				$data['text'] .= @$data['from_location'] . "\n";
				$data['text'] .= @$data['date'] . "\n";
				$data['text'] .= @$data['to_str'] . "\n";
				$data['text'] .= @$data['title'] . "\n";
				$data['text'] .= @$data['content'] . "\n";
				$data['text'] .= @$data['from_signature'] . "\n";
				
				$data['saved'] = $saved;
				
									
				if( 
					isset($data['created_at']) && 
					( $created_at = strtotime( $data['created_at'] ) ) 
				)
					$data['created_at'] = date($mask, $created_at);
					
				if( 
					isset($data['modified_at']) && 
					( $modified_at = strtotime( $data['modified_at'] ) ) 
				)
					$data['modified_at'] = date($mask, $modified_at);
											
				
			    
			    $ES = ConnectionManager::getDataSource('MPSearch');
				
				debug( $data );
				
				$response = $ES->API->index(array(
					'index' => 'mojepanstwo_v1',
					'type' => 'letters',
					'id' => $data['alphaid'],
					'body' => $data,
				));
				
				
				
			
			}		
						
		}
		
	}
	
	public function search($params) {
		
		$ES = ConnectionManager::getDataSource('MPSearch');
			
		$page = isset($params['page']) ? $params['page'] : 1;
		$from = ($page-1) * 20;		
		
		$filtered = array(
	        'filter' => array(
	            'and' => array(
	                'filters' => array(
	                    array(
	                        'term' => array(
	                        	'from_user_type' => $params['user_type'],
	                        ),
	                    ),
	                    array(
	                        'term' => array(
	                        	'from_user_id' => $params['user_id'],
	                        ),
	                    ),
	                ),
	                '_cache' => true,
	            ),
	        ),
	    );
	    
	    if( $params['q'] )
	    	$filtered['query'] = array(
		        'match' => array(
			        'text' => $params['q'],
		        ),
	        );
		
		$data = $ES->API->search(array(
			'index' => 'mojepanstwo_v1',
			'type' => 'letters',
			'body' => array(
				'from' => $from, 
				'size' => 20,
				'query' => array(
					'filtered' => $filtered,
				),
				'partial_fields' => array(
					'data' => array(
						'include' => array('id', 'alphaid', 'name', 'slug', 'date', 'created_at', 'modified_at', 'to_name', 'hash'),
					),
				),
				'sort' => array(
					'modified_at' => 'desc',
				),
			),
		));
		
		// debug($data); die();
		
		$items = array();
				
		foreach( $data['hits']['hits'] as $hit )
			$items[] = $hit['fields']['data'][0];
		
		return array(
			'performance' => array(
				'took' => $data['took'],
			),
			'pagination' => array(
				'page' => $page,
				'perPage' => 20,
				'total' => $data['hits']['total'],
			),
			'items' => $items,
		);
		
	}
	
	public function rename($id, $params) {
		
		if( !$params['name'] )
			return false;
		
		App::import('model','DB');
		$DB = new DB();
		
		$q = "UPDATE `pisma_documents` SET `name`='" . addslashes( $params['name'] ) . "' WHERE `alphaid`='" . addslashes($id) . "' AND `from_user_type`='" . addslashes( $params['user']['type'] ) . "' AND `from_user_id`='" . addslashes( $params['user']['id'] ) . "' LIMIT 1";
		$DB->query($q);
		
		if( $DB->getAffectedRows() ) {
			
			$ES = ConnectionManager::getDataSource('MPSearch');
			$ES->API->update(array(
			    'index' => 'mojepanstwo_v1',
			    'type' => 'letters',
			    'id' => $id,
			    'body' => array(
				    'doc' => array(
					    'name' => $params['name'],
				    ),
			    ),
		    ));
		    
		    return true;
			
		} else return true;
		
	}
	
	public function delete($id, $params) {
		
		App::import('model','DB');
		$DB = new DB();
		
		
		$item = $DB->selectAssoc("SELECT `id`, `saved` FROM `pisma_documents` WHERE `alphaid`='" . addslashes($id) . "' AND `from_user_type`='" . addslashes( $params['from_user_type'] ) . "' AND `from_user_id`='" . addslashes( $params['from_user_id'] ) . "' LIMIT 1");
		
		if( $item ) {
			
			$DB->q("UPDATE `pisma_documents` SET `deleted`='1', `deleted_at`=NOW() WHERE `id`='" . $item['id'] . "' LIMIT 1");
			
			
			$ES = ConnectionManager::getDataSource('MPSearch');
					
			$deleteParams = array();
			$deleteParams['index'] = 'mojepanstwo_v1';
			$deleteParams['type'] = 'letters';
			$deleteParams['id'] = $id;
			$deleteParams['ignore'] = array(404);
			
			$ES->API->delete($deleteParams);
						
			return 200;
			
		} else return 404;
				
	}
	
	public function send($id, $user, $params) {
		
		if( $pismo = $this->find('first', array(
	        'conditions' => array(
		        'deleted' => '0',
		        'id' => $id,
		        'from_user_type' => 'account',
		        'from_user_id' => $user['id'],
	        ),
        )) ) {
	        	        
	    	$pismo = $pismo['Document'];	    	
	    	App::uses('CakeEmail', 'Network/Email');
	    	
	    
	    	
			$Email = new CakeEmail('pisma');
			$Email->viewVars(array('pismo' => $pismo));
			$status = $Email->template('Pisma.pismo', 'Pisma.layout')
				->addHeaders(array('X-Mailer' => 'mojePaństwo'))
				->emailFormat('html')
				->subject($pismo['title'])
				->to($pismo['to_email'], $pismo['to_name'])
				// ->to('daniel.macyszyn@epf.org.pl', 'Daniel Macyszyn')
				->from('pisma@mojepanstwo.pl', 'Pisma | mojePaństwo')
				->replyTo($user['email'], $user['username'])
				->cc($user['email'], $user['username'])
				->send();    	    
    	    
    	    
    	    $ES = ConnectionManager::getDataSource('MPSearch');
			
		    $ES->API->update(array(
			    'index' => 'mojepanstwo_v1',
			    'type' => 'letters',
			    'id' => $id,
			    'body' => array(
				    'doc' => array(
					    'sent' => true,
				    	'sent_at' => date('Ymd\THis\Z'),
				    ),
			    ),
		    ));
    	    
    	    $db = ConnectionManager::getDataSource('default');
    	    $db->query("UPDATE `pisma_documents` SET `sent`='1', `sent_at`=NOW() WHERE `alphaid`='" . addslashes( $id ) . "'");

    	    return (boolean) $status;
	        
        } else throw new NotFoundException();
				
	}
	
	public function transfer_anonymous($anonymous_user_id, $user_id) {
				
		if(
			( $db = ConnectionManager::getDataSource('default') ) && 
			( $where = "from_user_type='anonymous' AND from_user_id='" . addslashes( $anonymous_user_id ) . "'" ) && 
			( $ids = $db->query("SELECT alphaid, saved FROM pisma_documents WHERE $where") ) 
		) {
			
			$ES = ConnectionManager::getDataSource('MPSearch');
			
			foreach( $ids as $id ) {
				if( $id['pisma_documents']['saved'] ) {
				    $ES->API->update(array(
					    'index' => 'mojepanstwo_v1',
					    'type' => 'letters',
					    'id' => $id['pisma_documents']['alphaid'],
					    'body' => array(
						    'doc' => array(
							    'from_user_type' => 'account',
						    	'from_user_id' => $user_id,
						    ),
					    ),
				    ));
			    }
			}
			
			$db->query("UPDATE pisma_documents SET `from_user_type`='account', `from_user_id`='" . addslashes( $user_id ) . "' WHERE $where");
			
			return true;
			
		} else return false;
		
	}

}
