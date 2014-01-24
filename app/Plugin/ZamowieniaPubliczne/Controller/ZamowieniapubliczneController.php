<?php

class ZamowieniapubliczneController extends AppController
{

    public function stats()
    {
        $stats = $this->Zamowieniapubliczne->getStats();

        $this->set('stats', $stats);
        $this->set('_serialize', array('stats'));
    }

} 