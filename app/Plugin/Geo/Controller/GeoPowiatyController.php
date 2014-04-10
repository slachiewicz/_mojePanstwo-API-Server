<?php

class GeoPowiatyController extends AppController
{

	public $uses = array('Geo.GeoPowiat');
	
    public function index()
    {
    	
    	$id = $this->request->params['id'];
    	
        $powiaty = $this->GeoPowiat->find('all', array(
            'conditions' => array(
                'GeoPowiat.w_id' => $id,
            ),
            'order' => array(
                'GeoPowiat.nazwa' => 'asc'
            ),
        ));
        $this->set(array(
            'powiaty' => $powiaty,
            '_serialize' => array('powiaty'),
        ));
    }
} 