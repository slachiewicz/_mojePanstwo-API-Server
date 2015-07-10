<?php

App::uses('AppModel', 'Model');

class BdlPodgrupy extends AppModel {

    public $useTable = 'BDL_podgrupy';

    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function update($data) {
        $id = (int) $this->request['id'];
        if(!$id)
            return false;

        $this->read(null, $id);
        $this->set(array(
            'opis' => $data['opis'],
            'nazwa' => $data['nazwa']
        ));
        $this->save();

        return true;
    }

}