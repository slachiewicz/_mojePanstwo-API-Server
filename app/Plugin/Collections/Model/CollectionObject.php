<?php

class CollectionObject extends AppModel {

    public $useTable = 'collection_object';

    public $validate = array(
        'collection_id' => array(
            'required' => true
        ),
        'object_id' => array(
            'required' => true
        )
    );

}