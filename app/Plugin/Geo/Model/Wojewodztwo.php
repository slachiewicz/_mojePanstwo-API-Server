<?php

class Wojewodztwo extends AppModel
{
    public $useTable = 'wojewodztwa';
    public $virtualFields = array(
        'spat' => 'AsText(centroid(spat))',
        'typ' => 2,
    );

} 