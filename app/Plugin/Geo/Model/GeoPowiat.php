<?php

class GeoPowiat extends AppModel
{
    public $useTable = 'pl_powiaty';
    public $hasMany = array('PowiatGrodzkiGminy' => array('foreignKey' => 'powiat_id', 'className' => 'Geo.PowiatGrodzkiGminy'));
    public $virtualFields = array(
        'spat0' => 'AsText( centroid( Powiat.spat0 ) )',
        'typ' => 3,
    );
} 