<?php

class GlosyController extends AppController
{

    public $uses = array('Krakow.UserVotes');
    public $components = array('RequestHandler');


    public function save($druk_id) {
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();

        $this->setSerialized('response', $this->UserVotes->vote(
            (int) $this->Auth->user('id'),
            (int) $druk_id,
            (int) $this->data['vote']
        ));
    }

    public function view($druk_id) {
        $this->setSerialized(
            'response',
            $this->UserVotes->getVotes(
                $druk_id,
                (int) $this->Auth->user('id')
            )
        );
    }

}