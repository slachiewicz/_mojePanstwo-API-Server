<?php

class Act extends AppModel
{

    public $useTable = 'prawo_ustawy_glowne';

    public function find($type = 'first', $queryData = array())
    {

        $queryData = array_merge_recursive(array(
            'fields' => array(
                'Act.id',
                'Act.nazwa',
                'Act.adres',
                'Act.kod_id',
                'Act.kod',
            ),
            'order' => array('Act.adres' => 'asc'),
            'limit' => 10,
        ), $queryData);

        return parent::find($type, $queryData);
    }

    public function getDoc($id)
    {

        $data = $this->query("SELECT `s_dokumenty`.`id`, `s_dokumenty`.`url`
	    FROM `prawo_ustawy_glowne` 
	    JOIN `s_dokumenty` 
	    ON `prawo_ustawy_glowne`.`dokument_id` = `s_dokumenty`.`id` 
	    WHERE `prawo_ustawy_glowne`.`id`='" . addslashes($id) . "'");

        if ($data)
            return $data[0]['s_dokumenty'];

        return array();

    }

} 