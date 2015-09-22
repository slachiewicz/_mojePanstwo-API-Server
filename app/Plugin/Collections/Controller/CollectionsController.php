<?php

App::uses('AppController', 'Controller');

/**
 * @property Collection Collection
 */
class CollectionsController extends AppController {

    public $uses = array('Collections.Collection');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException;
    }

    public function get() {
        $this->set('response', $this->Collection->find('all', array(
            'conditions' => array(
                'Collection.user_id' => $this->Auth->user('id')
            )
        )));
        $this->set('_serialize', 'response');
    }

    public function create() {
        $data = array_merge($this->request->data, array(
            'user_id' => $this->Auth->user('id'),
        ));

        $this->Collection->set($data);
        if($this->Collection->validates()) {
            $response = $this->Collection->save(array(
                'Collection' => $data
            ));
        } else {
            $response = $this->Collection->validationErrors;
        }

        $this->set('response', $response);
        $this->set('_serialize', 'response');
    }

}