<?php

class BdlWariacje extends AppModel {

    public $useTable = false;

    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function update($data) {
        $id = (int) $this->request['id'];
        if(!$id)
            return false;

        return true;
    }

}