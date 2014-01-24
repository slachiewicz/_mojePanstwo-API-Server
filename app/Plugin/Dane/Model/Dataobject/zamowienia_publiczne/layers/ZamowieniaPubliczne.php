<?php

class ZamowieniaPubliczne extends AppModel
{
    public function details()
    {
        $this->useTable = 'uzp_dokumenty';
        return $this->find('first', array(
            'conditions' => array(
                'id' => $this->id,
            ),
            'fields' => array('przedmiot'),
        ));
    }
} 