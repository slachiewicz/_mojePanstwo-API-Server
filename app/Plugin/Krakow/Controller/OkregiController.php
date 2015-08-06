<?php

class OkregiController extends AppController {

    public $uses = array('Krakow.KrakowOkregi');

    public function get() {
        $this->set('data', $this->KrakowOkregi->getOkregi());
        $this->set('_serialize', 'data');
    }

}