<?php

class KeywordsController extends AppController
{

    public function index()
    {

        $types = $this->Keyword->index();
        
        $this->set(array(
	    	'types' => $types,
	    	'_serialize' => 'types',
	    ));

    }

} 