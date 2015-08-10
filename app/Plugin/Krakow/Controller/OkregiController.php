<?php

class OkregiController extends AppController {

    public $uses = array('Krakow.KrakowOkregi');

    public function get($id = 0) {

        if($id) {
            $data = $this->KrakowOkregi->getOkreg($id);
        } else {
            $data = $this->KrakowOkregi->getOkregi();
        }

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

}