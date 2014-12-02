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
	);
	
	
        	
	
    /**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'alphaid' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 5),
			),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
            ),
		),
		'hash' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 32),
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
		'from_user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => true,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'from_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
                'required' => true,
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
                'required' => true,
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
		'to_dataset' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
                'required' => true,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'to_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => true,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			)
		),
		'sent' => array(
			'boolean' => array(
				'rule' => array('boolean'),
                'required' => true,
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
				
		if( ($data = $this->data['Document']) && isset($data['id']) && $data['id'] ) {
			
			$data['text'] = $data['name'] . "\n";
			$data['text'] .= $data['from_str'] . "\n";
			$data['text'] .= $data['from_location'] . "\n";
			$data['text'] .= $data['date'] . "\n";
			$data['text'] .= $data['to_str'] . "\n";
			$data['text'] .= $data['title'] . "\n";
			$data['text'] .= $data['content'] . "\n";
			$data['text'] .= $data['from_signature'] . "\n";
			$data['created_at'] = str_replace(' ', 'T', $data['created_at']);
			$data['modified_at'] = str_replace(' ', 'T', $data['modified_at']);
			$data['sent'] = (boolean) @$data['sent'];
			
			App::Import('ConnectionManager');
			$ES = ConnectionManager::getDataSource('MPSearch');
			
			$response = $ES->API->index(array(
				'index' => 'mojepanstwo_v1',
				'type' => 'letters',
				'id' => $data['id'],
				'body' => $data,
			));
						
		}
		
	}
	
	public function search($user_id, $params = array()) {
		
		App::Import('ConnectionManager');
		$ES = ConnectionManager::getDataSource('MPSearch');
				
		$data = $ES->API->search(array(
			'index' => 'mojepanstwo_v1',
			'body' => array(
				'from' => 0, 
				'size' => 20,
				'query' => array(
					'filtered' => array(
				        'filter' => array(
				            'and' => array(
				                'filters' => array(
				                    array(
				                        'term' => array(
				                        	'_type' => 'letters',
				                        ),
				                    ),
				                    array(
				                        'term' => array(
				                        	'from_user_id' => $user_id,
				                        ),
				                    ),
				                ),
				                '_cache' => true,
				            ),
				        ),
				    ),
				),
				'partial_fields' => array(
					'data' => array(
						'include' => array('id', 'alphaid', 'name', 'slug', 'date', 'created_at', 'modified_at'),
					),
				),
				'sort' => array(
					'created_at' => 'desc',
				),
			),
		));

				
		$items = array();
		
		foreach( $data['hits']['hits'] as $hit )
			$items[] = $hit['fields']['data'][0];
		
		return array(
			'items' => $items,
		);
		
	}

}
