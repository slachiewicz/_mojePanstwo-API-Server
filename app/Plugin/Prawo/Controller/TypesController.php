<?php

class TypesController extends AppController
{

    public function index()
    {

        $types = $this->Type->index();
        
        $this->set(array(
	    	'types' => $types,
	    	'_serialize' => 'types',
	    ));

    }

} 