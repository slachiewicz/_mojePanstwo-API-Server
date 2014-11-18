<?php

class WydatkiposlowController extends AppController
{

    public function stats()
    {
        $stats = $this->Wydatkiposlow->getStats();

        $this->set('stats', $stats);
        $this->set('_serialize', 'stats');
    }
    
} 