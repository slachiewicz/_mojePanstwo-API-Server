<?php

class UserPhrase extends AppModel
{

    public $virtualFields = array(
        'phrase_id' => 'UserPhrase.alert_id'
    );

    public $belongsTo = array(
        'Phrase' => array(
            'foreignKey' => 'alert_id',
            'type' => 'inner',
            'className' => 'Powiadomienia.Phrase',
            'conditions' => array(
                'UserPhrase.deleted' => '0',
            ),
            'fields' => array('q'),
        )
    );
    public $useTable = 'm_alerts-users';


    function find($type = 'first', $queryData = array())
    {

        $queryData = array_merge_recursive(array(
            'fields' => array('Phrase.id', 'Phrase.q', 'id', 'alerts_unread_count', 'alerts_read_count'),
            'order' => array('Phrase.q' => 'asc'),
            'limit' => 10,
        ), $queryData);

        return parent::find($type, $queryData);

    }
}



/*
return $this->DB->selectAssoc("SELECT `m_alerts`.`id`, `m_alerts-users`.`alerts_unread_count`, `m_alerts-users`.`alerts_read_count` FROM `m_alerts` JOIN `m_alerts-users` ON `m_alerts`.`id` = `m_alerts-users`.`alert_id` WHERE `m_alerts`.`q`='" . addslashes( $_PARAMS ) . "' AND `m_alerts`.`stream_id`='" . $stream_id . "' AND `m_alerts-users`.`user_id`='" . $this->USER['id'] . "' AND `m_alerts-users`.`deleted` = '0'");
*/