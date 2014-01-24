<?php


class ServicesController extends PaszportAppController
{
    public $users = array('Paszport.Service', 'Paszport.User');

    public function index()
    {
        $this->data = $this->Service->find('all', array(
            'contain' => array(
                'User' => array(
                    'fields' => array('User.id'),
                    'conditions' => array('User.id' => $this->Auth->user('id')),
                ),
            ),
        ));
        $this->loadModel('OAuth.AccessToken');
        $tokens = $this->AccessToken->find('all', array(
            'conditions' => array(
                'AccessToken.user_id' => $this->Auth->user('id'),
            ),
        ));
        $this->set('tokens', $tokens);
        $this->set('title_for_layout', 'LC_PASZPORT_OUR_SERVICES');
    }
}