<?php

class DeclarationsController extends AppController
{
    public function add() {
	    
	    $msg = (boolean) $this->Declaration->save($this->request->data, false, array(
		    'organization', 'firstname', 'lastname', 'position', 'email', 'phone'
	    ));
	    
	    $this->set('message', $msg);
	    $this->set('_serialize' , 'message');
	    
    }
} 