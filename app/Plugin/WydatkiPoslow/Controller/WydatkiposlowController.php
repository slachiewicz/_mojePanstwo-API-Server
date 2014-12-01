<?php

class WydatkiposlowController extends AppController
{

    public function stats()
    {
        $this->set('stats', $this->Wydatkiposlow->getStats());
        $this->set('_serialize', 'stats');
    }
    	
    public function category($id)
    {
        $this->set('data', $this->Wydatkiposlow->getCategory($id));
        $this->set('_serialize', 'data');
    }
    
} 