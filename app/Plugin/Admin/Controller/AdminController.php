<?php

App::uses('AppController', 'Controller');

class AdminController extends AppController {

    public $uses = array('Paszport.User');
    public $components = array('RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
        if(!$this->isAuthorized())
            throw new ForbiddenException();
    }

    public function modelCall() {
        $results = false;
        $data = array('class', 'method', 'arguments');
        foreach($data as $name)
            if(!isset($this->data[$name]))
                throw new BadRequestException;

        try {
            $this->loadModel('Admin.' . $this->data['class']);
            if (method_exists($this->{$this->data['class']}, $this->data['method'])) {
                $results = call_user_func_array(
                    array(
                        $this->{$this->data['class']},
                        $this->data['method']
                    ),
                    $this->data['arguments']
                );
            }

        } catch (MissingModelException $e) {
            throw new BadRequestException;
        }

        $this->set('results', $results);
        $this->set('_serialize', array('results'));
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

        return in_array('superuser', $roles);
    }

}