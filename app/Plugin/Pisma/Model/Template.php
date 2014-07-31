<?php
App::uses('AppModel', 'Model');
/**
* Template Model
*
*/
class Template extends AppModel {

    public $useTable = 'pisma_templates';
    public $recursive = -1;

    /**
    * Display field
    *
    * @var string
    */
    public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
                'required' => true,
                'allowEmpty' => false,

				//'message' => 'Your custom message here',
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'content' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
                'required' => true,
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,

				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

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
