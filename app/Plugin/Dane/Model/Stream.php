<?php

class Stream extends AppModel
{
    public $useTable = 'm_streams';
    public $actsAs = array('Containable');
    public $hasAndBelongsToMany = array(
        'Dataset' => array(
            'className' => 'Dane.Dataset',
            'joinTable' => 'datasets-streams',
            'foreignKey' => 'stream_id',
            'associationForeignKey' => 'dataset_id'
        ),
    );
} 