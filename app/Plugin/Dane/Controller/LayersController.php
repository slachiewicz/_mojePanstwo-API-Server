<?php

class LayersController extends AppController
{
    public function index($alias)
    {
        $layers = $this->Layer->find('all', array(
            'conditions' => array(
                'Layer.base_alias' => $alias,
            ),
        ));

        $this->set(array(
            'layers' => $layers,
            '_serialize' => array('layers'),
        ));
    }
} 