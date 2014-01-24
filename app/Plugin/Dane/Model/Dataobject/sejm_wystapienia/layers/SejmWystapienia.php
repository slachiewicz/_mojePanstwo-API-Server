<?php

class SejmWystapienia extends AppModel
{

    public function html()
    {
        $this->useTable = 'stenogramy_wystapienia';
        return $this->find('first', array(
            'conditions' => array(
                'id' => $this->id,
            ),
        ));
    }
} 