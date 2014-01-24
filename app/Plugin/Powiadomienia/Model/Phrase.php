<?php

class Phrase extends AppModel
{

    public $useTable = 'm_alerts';
    public $belongsTo = array('UserPhrase' => array('foreignKey' => 'alert_id', 'className' => 'Powiadomienia.UserPhrase'));
    public $paginate = array(
        'limit' => 10,
    );

} 