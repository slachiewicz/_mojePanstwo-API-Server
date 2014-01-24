<?php

class Gmina extends ObszaryAppModel
{
    public $useTable = 'pl_gminy';
    public $belongsTo = array(
        'Powiat' => array(
            'foreignKey' => 'pl_powiat_id',
            'className' => 'Obszary.Powiat'
        ),
        'Wojewodztwo' => array(
            'foreignKey' => 'w_id',
            'className' => 'Obszary.Wojewodztwo',
        ),
    );

} 