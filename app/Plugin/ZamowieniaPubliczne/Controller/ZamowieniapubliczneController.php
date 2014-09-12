<?php

class ZamowieniapubliczneController extends AppController
{

    public function stats()
    {
        $stats = $this->Zamowieniapubliczne->getStats();

        $this->set('stats', $stats);
        $this->set('_serialize', 'stats');
    }
    
    public function newstats()
    {
        $data = $this->Zamowieniapubliczne->getNewStats();

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

} 