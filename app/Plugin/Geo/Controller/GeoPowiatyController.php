<?php

class GeoPowiatyController extends AppController
{
    public function index($id)
    {
        $powiaty = $this->GeoPowiat->find('all', array(
            'conditions' => array(
                'Powiat.w_id' => $id,
            ),
            'order' => array(
                'Powiat.nazwa' => 'asc'
            ),
        ));
        $this->set(array(
            'powiaty' => $powiaty,
            '_serialize' => array('powiaty'),
        ));
    }
} 