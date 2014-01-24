<?php

class AlertobjectsController extends AppController
{
    public $uses = array('Powiadomienia.Alertobject');

    public function index()
    {
        $user_id = $this->user_id;
        $stream_id = 1;

        $offset = (isset($this->data['offset'])) ? $this->data['offset'] : 0;
        $limit = 20;

        $ids = (isset($this->data['ids'])) ? $this->data['ids'] : array();
        if ($ids && !is_array($ids))
            $ids = array($ids);

        $visited = (isset($this->data['visited'])) ? $this->data['visited'] : 0;


        $search = $this->Alertobject->find('all', array(
            'conditions' => array(
                'user_id' => $user_id,
                'stream_id' => $stream_id,
                'visited' => $visited,
                'keyword_id' => $ids,
            ),
            'offset' => $offset,
            'limit' => $limit,
        ));

        $this->set(array(
                'search' => $search,
                '_serialize' => array('search'),
            )
        );


    }

    public function flagObjects()
    {
        $stream_id = 1;
        $user_id = $this->user_id;
        $ids = (isset($this->data['ids'])) ? $this->data['ids'] : array();
        $q = "UPDATE `m_user-objects`
		  	JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
		  	JOIN `m_alerts-users` ON `m_alerts-users`.`alert_id`=`m_alerts-objects`.`alert_id` AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
		  	JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
		  	SET `m_alerts-users`.`analiza`='1', `m_alerts-users`.`analiza_ts`=NOW(), `m_alerts-users`.`alerts_unread_count`='0', `m_user-objects`.`visited`='1', `m_user-objects`.`visited_ts`=NOW()
		  	WHERE `m_alerts`.`stream_id`='$stream_id' AND `m_alerts-users`.`deleted`='0' AND `m_user-objects`.`user_id`='" . $user_id . "' AND `m_user-objects`.`visited`='0' AND (`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $ids) . "')";
//                if ($action_max)
//                    $q .= " AND `m_user-objects`.`object_id`<='$action_max'";

//                $this->DB->q($q);
//                $this->S('me/alerts/count');
        $this->Dataobject->query($q);
    }

    public function flagObject()
    {
        $user_id = $this->user_id;
        $object_id = $this->params->object_id;
        //@TODO : to w koncu jest oznaczanie pojedynczego obiektu czy nie ?
    }
} 