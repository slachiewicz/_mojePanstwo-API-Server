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

    /**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
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
}
