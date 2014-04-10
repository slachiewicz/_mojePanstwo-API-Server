<?php

class GeoWojewodztwo extends AppModel
{
    public $useTable = 'wojewodztwa';
    public $virtualFields = array(
        'spat' => 'AsText(centroid(spat))',
        'typ' => 2,
    );

} 