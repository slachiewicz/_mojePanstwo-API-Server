<?php

class Gmina extends AppModel
{
    public $useTable = 'pl_gminy';
    public $virtualFields = array(
        'typ' => 4,
        'spat0' => 'AsText( centroid( spat0 ) )',

    );
} 