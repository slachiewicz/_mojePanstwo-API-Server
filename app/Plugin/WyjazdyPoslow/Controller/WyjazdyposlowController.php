<?php

class WyjazdyposlowController extends AppController
{

    public function stats()
    {
        $stats = $this->Wyjazdyposlow->getStats();

        $this->set('stats', $stats);
        $this->set('_serialize', 'stats');
    }

    public function world() {
        $this->setSerialized('ret', $this->Wyjazdyposlow->getWorldStats());
    }

    public function countryDetails() {
        $this->setSerialized('ret', $this->Wyjazdyposlow->getCountryDetails($this->request->params['countrycode']));
    }
} 