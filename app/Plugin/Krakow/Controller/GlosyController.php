<?php

class GlosyController extends AppController
{

    public $uses = array('Krakow.UserVotes');
    public $components = array('RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();
    }

    public function save($druk_id) {
        $this->UserVotes->create();
        $this->setSerialized('response', $this->UserVotes->save(array(
            'user_id' => (int) $this->Auth->user('id'),
            'druk_id' => (int) $druk_id,
            'vote' => (int) $this->data['vote'],
            'vote_ts' => date('Y-m-d H:i:s')
        )));
    }

    public function view($druk_id) {
        $this->setSerialized('response', $this->UserVotes->find('count', array(
                'fields' => 'DISTINCT vote',
                'conditions' => array('druk_id' => $druk_id)
            )
        ));
    }

}