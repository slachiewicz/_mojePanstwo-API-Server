<?php

class DeclarationsController extends AppController {

    public $uses = array('NGO.Declaration');

    public function add() {
        $this->Declaration->create();
        if($this->Declaration->save($this->request->data, array(
            'fieldList' => array(
                'organization',
                'firstname',
                'lastname',
                'position',
                'email',
                'phone'
            )
        ))) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

}