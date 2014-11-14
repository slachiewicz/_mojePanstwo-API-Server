<?php

class WyjazdyposlowController extends AppController
{

    public function stats()
    {
        $stats = $this->Wyjazdyposlow->getStats();

        $this->set('stats', $stats);
        $this->set('_serialize', 'stats');
    }

} 