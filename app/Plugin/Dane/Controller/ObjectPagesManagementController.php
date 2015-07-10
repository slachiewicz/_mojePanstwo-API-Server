<?php

class ObjectPagesManagementController extends AppController {

    public $uses = array('Dane.ObjectUser', 'Dane.ObjectPage', 'Paszport.User');
    public $components = array('RequestHandler');

    public function __construct($request, $response) {
        parent::__construct($request, $response);
    }

    public function beforeFilter() {
        parent::beforeFilter();
        if(!$this->isAuthorized())
            throw new ForbiddenException();

        $this->ObjectPage->setRequest($this->request);
    }

    public function setLogo() {
        $this->ObjectPage->setLogo(true);
        $this->setSerialized('success', true);
    }

    public function setCover() {
        $this->ObjectPage->setCover(true, $this->data['credits']);
        $this->setSerialized('success', true);
    }

    public function deleteLogo() {
        $this->ObjectPage->setLogo(false);
        $this->setSerialized('success', true);
    }

    public function deleteCover() {
        $this->ObjectPage->setCover(false);
        $this->setSerialized('success', true);
    }

    public function isEditable() {
        $this->setSerialized('isEditable', true);
    }

    private function isAuthorized() {
        if($this->Auth->user('type') != 'account')
            return false;

        $this->User->recursive = 2;
        $user = $this->User->findById(
            $this->Auth->user('id')
        );

        if(!$user)
            return false;

        $roles = array();
        foreach($user['UserRole'] as $role) {
            $roles[] = $role['Role']['name'];
        }

        if(in_array('superuser', $roles)) {
            return true;
        } else {
            $object = $this->ObjectUser->find('first', array(
                'conditions' => array(
                    'ObjectUser.dataset' => $this->request['dataset'],
                    'ObjectUser.object_id' => $this->request['object_id'],
                    'ObjectUser.user_id' => $user['User']['id']
                )
            ));

            if(
                $object['ObjectUser']['role'] == '1' || // owner
                $object['ObjectUser']['role'] == '2'    // administrator
            ) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

}