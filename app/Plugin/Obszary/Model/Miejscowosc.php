<?php

class Miejscowosc extends ObszaryAppModel
{
    public $useTable = 'pl_miejscowosci';
    public $belongsTo = array(
        'Gmina' => array(
            'foreignKey' => 'gmina_id',
            'className' => 'Obszary.Gmina',
        ),
        'Powiat' => array(
            'foreignKey' => 'powiat_id',
            'className' => 'Obszary.Powiat',
        ),
        'Wojewodztwo' => array(
            'foreignKey' => 'woj_id',
            'className' => 'Obszary.Wojewodztwo',
        )
    );
} 