<?php

class PowiadomieniaGroup extends AppModel
{
    public $useTable = 'm_alerts_groups';
    
    public $defaultFields = array('id', 'title', 'slug', 'user_id', 'alerts_unread_count');
    
    public $order = array("PowiadomieniaGroup.ord" => "asc");
    
    public $_schema = array(
	    'id',
	);  
    
    // public $belongsTo = array('UserPhrase' => array('foreignKey' => 'alert_id', 'className' => 'Powiadomienia.UserPhrase'));
    
    public $paginate = array(
        'limit' => 50,
    );
    
    public function find($type = 'first', $query = array())
    {
	    if( !isset($query['fields']) || empty($query['fields']) )
	    	$query['fields'] = $this->defaultFields;
	    
	    return parent::find($type, $query);
	    
    }
} 