<?php

class LogsController extends PaszportAppController
{
    public $helpers = array('Paginator', 'Time');

    public function index()
    {
        $id = $this->user_id;
        $this->paginate = array(
            'recursive' => -2,
            'conditions' => array(
                'Log.user_id' => $id,
            ),
            'order' => array(
                'Log.created' => 'DESC',
            ),
            'limit' => 10

        );
        $this->data = $this->paginate('Log');
        $this->set(array(
            'logs' => $this->data,
            '_serialize' => array('logs'),
        ));
    }
}