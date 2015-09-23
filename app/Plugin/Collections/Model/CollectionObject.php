<?php

class CollectionObject extends AppModel {

    public $useTable = 'collection_object';

    public $validate = array(
        'collection_id' => array(
            'rule' => 'notEmpty',
            'required' => true
        ),
        'object_id' => array(
            'rule' => 'notEmpty',
            'required' => true
        )
    );

}