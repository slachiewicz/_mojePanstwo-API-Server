<?php

class FieldsController extends AppController
{
    public function index($alias)
    {
        $fields = $this->Field->find('all', array(
            'conditions' => array(
                'Field.base_alias' => $alias,
            ),
        ));

        $this->set(array(
            'fields' => $fields,
            '_serialize' => array('fields'),
        ));
    }
} 