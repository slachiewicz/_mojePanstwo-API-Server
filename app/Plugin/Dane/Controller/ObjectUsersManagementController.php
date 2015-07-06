<?php

class ObjectUsersManagementController extends AppController {

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

    public function index() {
        $results = $this->ObjectUser->find('all', array(
            'fields' => array(
                'ObjectUser.role',
                'User.id',
                'User.email',
                'User.username',
                'User.photo_small',
            ),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ObjectUser.user_id = User.id'
                    )
                )
            ),
            'conditions' => array(
                'ObjectUser.dataset' => $this->request['dataset'],
                'ObjectUser.object_id' => $this->request['object_id']
            )
        ));

        $users = array();
        foreach($results as $row) {
            $users[] = array(
                'id' => $row['User']['id'],
                'email' => $row['User']['email'],
                'role' => $row['ObjectUser']['role'],
                'username' => $row['User']['username'],
                'photo_small' => $row['User']['photo_small']
            );
        }

        $this->setSerialized('users', $users);
    }

    public function add() {
        $success = false;

        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.email' => $this->data['email']
            )
        ));

        if($user) {
            $this->ObjectUser->save(array(
                'ObjectUser' => array(
                    'dataset' => $this->request['dataset'],
                    'object_id' => $this->request['object_id'],
                    'user_id' => $user['User']['id'],
                    'role' => $this->data['role']
                )
            ));

            $this->ObjectPage->whenUserWasAdded();
            $success = true;
        }

        $this->setSerialized('success', $success);
    }

    public function edit() {
        $success = false;

        $object = $this->ObjectUser->find('first', array(
            'conditions' => array(
                'ObjectUser.dataset' => $this->request['dataset'],
                'ObjectUser.object_id' => $this->request['object_id'],
                'ObjectUser.user_id' => $this->request['user_id']
            )
        ));

        if($object) {
            $this->ObjectUser->read(null, $object['ObjectUser']['id']);
            $this->ObjectUser->set(array(
                'role' => $this->data['role']
            ));
            $this->ObjectUser->save();

            $success = true;
        }

        $this->setSerialized('success', $success);
    }

    public function delete() {
        $success = false;

        $object = $this->ObjectUser->find('first', array(
            'conditions' => array(
                'ObjectUser.dataset' => $this->request['dataset'],
                'ObjectUser.object_id' => $this->request['object_id'],
                'ObjectUser.user_id' => $this->request['user_id']
            )
        ));

        if($object) {
            $this->ObjectUser->delete($object['ObjectUser']['id']);

            $count = (int) $this->ObjectUser->find('count', array(
                'conditions' => array(
                    'ObjectUser.dataset' => $this->request['dataset'],
                    'ObjectUser.object_id' => $this->request['object_id']
                )
            ));

            if(!$count)
                $this->ObjectPage->whenUsersWasDeleted();


            $success = true;
        }

        $this->setSerialized('success', $success);
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

            if($object['ObjectUser']['role'] == '1') { // owner
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

}