<?php

class KeysController extends PaszportAppController
{
    public $uses = array('Paszport.Key', 'Paszport.User');

    public function index($id)
    {

        $this->data = $this->Key->find('all', array(
            'conditions' => array('User.id' => $id),
            'contain' => array('User'),
        ));
        $this->set(array(
            'keys' => $this->data,
            '_serialize' => array('keys'),
        ));
    }

    public function add()
    {

        if ($this->data) {
            $to_save = $this->data;
//            $to_save['Key']['user_id'] = $id;
            $to_save['Key']['key'] = md5(mktime() . $this->data['Key']['user_id']);
            if ($this->Key->save($to_save)) {
                $key = $this->Key->read();
                $this->set(array(
                    'key' => $key,
                    '_serialize' => array('key'),
                ));
            }
        }
    }

    public function delete($id = null)
    {
        if (is_null($id)) {
            $this->redirect(array('action' => 'index'));
        }
        $this->Key->id = $id;
        $this->Key->delete();
        exit();
    }
}