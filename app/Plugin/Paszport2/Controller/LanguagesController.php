<?php

class LanguagesController extends PaszportAppController
{
    public function index($list = false)
    {
        if ($list) {
            $language = $this->Language->find('list', array('fields' => array('id', 'label')));
        } else {
            $language = $this->Language->find('all');
        }
        $this->set('languages', $language);
        $this->set('_serialize', array('languages'));
    }
} 