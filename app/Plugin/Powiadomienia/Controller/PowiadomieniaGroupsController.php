<?php

class PowiadomieniaGroupsController extends AppController
{
    // public $uses = array('Powiadomienia.UserPhrase', 'Powiadomienia.Phrase');
	public $components = array('RequestHandler');
	// public $uses = array('PowiadomieniaGroup');
	
    public function index()
    {
    	    	   	    	 	
        $groups = $this->PowiadomieniaGroup->find('all', array(
            'conditions' => array(
                'user_id' => $this->user_id,
            ),
        ));
               
        $this->set('groups', $groups);
        $this->set('_serialize', 'groups');

    }
    
    public function view($id)
    {
	    	    	    
	    $group = $this->PowiadomieniaGroup->find('first', array(
            'conditions' => array(
                'PowiadomieniaGroup.id' => $id,
                'PowiadomieniaGroup.user_id' => $this->Auth->user('id'),
            ),
        ));
               
        $this->set('group', $group);
        $this->set('_serialize', 'group');
	    
    }
    
    public function add()
    {		
				
		if( isset( $this->request->data['group'] ) ) {
	    	
		    $this->PowiadomieniaGroup->create( $this->request->data['group'] );        	        
	        $this->set(array(
	        	'status' => $this->PowiadomieniaGroup->save(),
	        	'id' => $this->PowiadomieniaGroup->id,
	        	'_serialize' => array('status', 'id'),
	        ));
        
        }
    }
    
    public function flag()
    {
    	$action = @$this->request->query['action'];
        $result = $this->PowiadomieniaGroup->flag($this->params->group_id, $action);
        
        $this->set('result', $result);  
	    $this->set('_serialize', 'result');
    }
	
	public function delete($id)
	{
		$this->PowiadomieniaGroup->delete($id);
	}
	
} 