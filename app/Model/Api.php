<?php
App::uses('AppModel', 'Model');
/**
* API that is available on server
*
*/
class Api extends AppModel {
    public $useTable = 'api_apis';

    public $displayField = 'name';
}
