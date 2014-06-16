<?php

class SejmometrController extends AppController
{

    public function autorzy_projektow($params = array())
    {
	    
	    $this->set('data', $this->Sejmometr->autorzy_projektow($params));
	    $this->set('_serialize', 'data');
	    
    }
    
    public function zawody()
    {
	    
	    $this->set('data', $this->Sejmometr->zawody());
	    $this->set('_serialize', 'data');
	    
    }
    
    public function stats()
    {
	    
	    $this->set('data', $this->Sejmometr->stats());
	    $this->set('_serialize', 'data');
	    
    }
    

}
