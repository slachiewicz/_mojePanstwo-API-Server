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
                'Address.adres',
                'Address.kod_id',
                'Address.kod',
            ),
            'order' => array('Address.adres' => 'asc'),
            'limit' => 10,
        ), $queryData);

        return parent::find($type, $queryData);
    }

} 