<?php

class GroupsController extends PaszportAppController
{
    public function index($list = false)
    {
        if ($list) {
            $group = $this->Group->find('list', array('fields' => array('id', 'label')));
        } else {
            $group = $this->Group->find('all');
        }
        $this->set('groups', $group);
        $this->set('_serialize', array('groups'));
    }
} 