<?php

class Doc extends AppModel
{

    public $useTable = 's_dokumenty';

    public $virtualFields = array(
        'filename' => 'plik',
        'fileextension' => 'plik_rozszerzenie',
        'filesize' => 'size',
    );

} 