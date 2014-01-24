<?php

class PowiatGrodzkiGminy extends AppModel
{
    public $belongsTo = array('Powiat' => array('foreignKey' => 'powiat_id', 'className' => 'Geo.Powiat'));
    public $useTable = 'pl_powiaty_grodzkie_gminy';
} 