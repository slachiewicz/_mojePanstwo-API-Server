<?php

App::uses('AppModel', 'Model');
class DataPl extends AppModel {

    public $useTable = 'BDL_data_pl';
    public $actsAs = array('Containable');
    
}