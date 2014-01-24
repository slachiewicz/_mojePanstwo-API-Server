<?php

class CodesController extends AppController
{

    public function view()
    {

        $id = @$this->request->params['id'];
        $id = (int)str_replace(array('-', ' ', '.', ','), '', $id);

        $this->set('search', $this->Code->find('first', array(
            'conditions' => array(
                'kod_int' => $id,
            ),
            'fields' => array('id', 'kod'),
        )));
        $this->set('_serialize', array('search'));

    }
} 