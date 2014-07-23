<?php

class TagsController extends AppController
{

    public function getExposed()
    {

        $tags = $this->Tag->getExposed();
        
        $this->set(array(
	    	'tags' => $tags,
	    	'_serialize' => 'tags',
	    ));

    }

} 