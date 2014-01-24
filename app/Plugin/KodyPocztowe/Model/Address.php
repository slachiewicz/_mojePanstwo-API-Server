<?php

class Address extends AppModel
{

    public $useTable = '_kody_pocztowe_pna';

    public function find($type = 'first', $queryData = array())
    {

        $queryData = array_merge_recursive(array(
            'fields' => array(
                'Address.id',
                'Address.nazwa',
                'Address.ulica',
                'Address.numery',
                'Address.kod_id',
                'Address.kod',
            ),
            'order' => array('ulica ASC', 'numery ASC')
        ), $queryData);

        return parent::find($type, $queryData);
    }

} 