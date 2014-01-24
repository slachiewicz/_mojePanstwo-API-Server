<?php

class Powiat extends ObszaryAppModel
{
    public $useTable = 'pl_powiaty';
    public $belongsTo = array(
        'Wojewodztwo' => array(
            'foreignKey' => 'w_id',
            'className' => 'Obszary.Wojewodztwo',
        ),
    );
} 