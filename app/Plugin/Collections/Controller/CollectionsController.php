<?php

App::uses('AppController', 'Controller');

/**
 * @property CollectionObject CollectionObject
 * @property Collection Collection
 */
class CollectionsController extends AppController {

    public $uses = array('Collections.Collection', 'Collections.CollectionObject');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException;
    }

    public function get($id) {
        $this->set('response', $this->Collection->find('all', array(
            'conditions' => array(
                'Collection.user_id' => $this->Auth->user('id')
            ),
            'joins' => array(
                array(
                    'table' => 'collection_object',
                    'alias' => 'CollectionObject',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CollectionObject.collection_id = Collection.id',
                        'CollectionObject.object_id' => (int) $id
                    )
                )
            ),
            'fields' => array('Collection.*', 'CollectionObject.*')
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

    public function addObject($id, $object_id) {
        $collection = $this->Collection->find('first', array(
            'conditions' => array(
                'Collection.id' => $id
            )
        ));

        if(!$collection)
            throw new NotFoundException;

        if($collection['Collection']['user_id'] != $this->Auth->user('id'))
            throw new ForbiddenException;

        $this->set('response', $this->CollectionObject->save(array(
            'CollectionObject' => array(
                'collection_id' => (int) $id,
                'object_id' => (int) $object_id
            )
        )));
        $this->set('_serialize', 'response');
    }

    public function removeObject($id, $object_id) {
        $collection = $this->Collection->find('first', array(
            'conditions' => array(
                'Collection.id' => $id
            )
        ));

        if(!$collection)
            throw new NotFoundException;

        if($collection['Collection']['user_id'] != $this->Auth->user('id'))
            throw new ForbiddenException;

        $this->set('response', $this->CollectionObject->query('DELETE FROM collection_object WHERE collection_id = ' . (int) $id . ' AND object_id = '. (int) $object_id));
        $this->set('_serialize', 'response');
    }
}