<?php

class ServiceUsersController extends PaszportAppController
{
    public $uses = array('Paszport.ServiceUser');

    public function add()
    {
        if ($this->data) {
            if ($this->ServiceUser->save($this->data)) {
                $this->set(array(
                    'serviceuser' => $this->ServiceUser->data,
                    '_serialize' => array('serviceuser'),
                ));
            }
        } else {
            exit();
        }
    }
} 