<?php

class SejmometrController extends AppController
{

    public function autorzy_projektow($params = array())
    {
	    
	    $this->set('data', $this->Sejmometr->autorzy_projektow($params));
	    $this->set('_serialize', 'data');
	    
    }

}
