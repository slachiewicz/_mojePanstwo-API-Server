<?php

class NewalertobjectsController extends AppController
{
    public $uses = array('Powiadomienia.Newalertobject');

    public function index()
    {
                
        $conditions = ( isset($this->request->query['conditions']) && is_array($this->request->query['conditions']) ) ? $this->request->query['conditions'] : array();
        
        $page = (isset($this->data['page']) && $this->data['page']) ? $this->data['page'] : 1;
        $limit = 20;
        $offset = $limit * ($page - 1);
			
		
        $group_id = (isset($conditions['group_id'])) ? $conditions['group_id'] : false;

        $mode = (isset($conditions['mode'])) ? $conditions['mode'] : 0;
		$visited = ($mode=='2') ? true : false;

        $search = $this->Newalertobject->find('all', array(
            'conditions' => array(
                'visited' => $visited,
                'group_id' => $group_id,
            ),
            'offset' => $offset,
            'limit' => $limit,
        ));

        $this->set(array(
                'search' => $search,
                '_serialize' => array('search'),
            )
        );


    }

    public function flag()
    {
    	$action = @$this->request->query['action'];
        $result = $this->Newalertobject->flag($this->params->object_id, $action);
        
        $this->set('result', $result);  
	    $this->set('_serialize', 'result');
    }
    
    public function flagAll()
    {
    	$action = @$this->request->query['action'];
    	$group_id = @$this->request->query['group_id'];
        
        if( $group_id ) {
        	
        	App::import('model', 'Powiadomienia.PowiadomieniaGroup');
        	$this->PowiadomieniaGroup = new PowiadomieniaGroup();
	        $result = $this->PowiadomieniaGroup->flag($group_id, $action);
	        
        } else {
	        $result = $this->Newalertobject->flagAll($action);
	    }
        
        $this->set('result', $result);  
	    $this->set('_serialize', 'result');
    }
} 