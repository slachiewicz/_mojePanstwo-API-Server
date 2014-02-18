<?php

class UserPhrasesController extends AppController
{
    public $uses = array('Powiadomienia.UserPhrase', 'Powiadomienia.Phrase');
	public $components = array('RequestHandler');
	
	
    public function index()
    {
        $phrases = $this->UserPhrase->find('all', array(
            'conditions' => array(
                'UserPhrase.user_id' => $this->user_id,
                'Phrase.stream_id' => $this->stream_id,
            ),
        ));
        $this->set('phrases', $phrases);
        $this->set('_serialize', array('phrases'));
    }

    public function add()
    {
        $user_id = $this->user_id;
        if ($this->data) {
            $result = $this->Phrase->query("SELECT `m_alerts`.id, `alerts-users`.user_id FROM `m_alerts` LEFT JOIN (SELECT * FROM `m_alerts-users` WHERE user_id='" . $user_id . "') as `alerts-users` ON `m_alerts`.id=`alerts-users`.alert_id WHERE `m_alerts`.q='" . addslashes($this->data['q']) . "'  LIMIT 1");
            if ($result[0]['alerts-users']['user_id']) {
//                $powiadomienia = array(array('Fraza' => array('id' => $result[0]['m_alerts']['id'])));
            } else {
                if ($result[0]['m_alerts']['id']) {
                    $alert_id = $result[0]['m_alerts']['id'];
                } else {
                    $this->Phrase->save(array(
                        'q' => $this->data['q'],
                    ));
                    $alert_id = $this->Phrase->id;
                }
                $this->UserPhrase->save(array(
                    'user_id' => $user_id,
                    'alert_id' => $alert_id,
                ));
//                $powiadomienia = array(array('Fraza' => array('id' => $alert_id)));
            }
        }
    }

    public function remove()
    {
        $user_id = $this->user_id;
        $phrase_id = $this->params->phrase_id;
        $obj = $this->UserPhrase->find('first', array(
            'conditions' => array(
                'UserPhrase.user_id' => $user_id,
                'UserPhrase.alert_id' => $phrase_id,
            ),
        ));
        if ($obj) {
            $this->UserPhrase->id = $obj['UserPhrase']['id'];
            $this->UserPhrase->delete();
        }
        $this->set(array(
            'powiadomienia' => array(),
            '_serialize' => array('powiadomienia'),
        ));
    }
    
    
    
    
    
    
    /*
public function markallread($user_id)
{
    if ($this->data) {
        if (!isset($this->data['ids']) || empty($this->data['ids'])) {
            $q = "UPDATE `m_user-objects`
          JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
          JOIN `m_alerts-users` ON `m_alerts-users`.`alert_id`=`m_alerts-objects`.`alert_id` AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
          JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
          SET `m_alerts-users`.`analiza`='1', `m_alerts-users`.`analiza_ts`=NOW(), `m_alerts-users`.`alerts_unread_count`='0', `m_user-objects`.`visited`='1', `m_user-objects`.`visited_ts`=NOW()
          WHERE `m_alerts-users`.`deleted`='0' AND `m_user-objects`.`user_id`='" . $user_id . "' AND `m_user-objects`.`visited`='0'";
            if ($this->data['action_max']) {
                $q .= " AND `m_user-objects`.`object_id`<='" . $this->data['action_max'] . "'";
            }
            $this->Frazy->query($q);
        } else if (is_array($this->data['ids']) && count($this->data['ids'])) {
            $q = "UPDATE `m_user-objects`
          JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
          JOIN `m_alerts-users` ON `m_alerts-users`.`alert_id`=`m_alerts-objects`.`alert_id` AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
          JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
          SET `m_alerts-users`.`analiza`='1', `m_alerts-users`.`analiza_ts`=NOW(), `m_alerts-users`.`alerts_unread_count`='0', `m_user-objects`.`visited`='1', `m_user-objects`.`visited_ts`=NOW() AND `m_alerts-users`.`deleted`='0' AND `m_user-objects`.`user_id`='" . $user_id . "' AND `m_user-objects`.`visited`='0' AND (`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $this->data['ids']) . "')";
            if ($this->data['action_max'])
                $q .= " AND `m_user-objects`.`object_id`<='" . $this->data['action_max'] . "'";
        }

    }
}

public function search($user_id)
{
    $sql_fields = "`m_alerts-objects`.`object_id` as '_object_id', `m_alerts-objects`.`score`, `m_alerts-objects`.`hl`, `objects`.`dataset`, `objects`.`object_id`, `api_datasets`.`results_class`";
    $sql_order = "`m_alerts-objects`.`object_id` DESC";

    $offset = $this->data['offset'];
    $limit = 10;
    $ids = (isset($this->data['ids'])) ? $this->data['ids']  : array();

    CakeLog::debug(print_r($this->data, true));
    if (empty($ids)) {

        $q = "SELECT $sql_fields
        FROM `m_user-objects`
        JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
        JOIN `m_alerts-users` ON `m_alerts-objects`.`alert_id`=`m_alerts-users`.alert_id AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
        JOIN `objects` ON `m_user-objects`.`object_id`=`objects`.`id`
        JOIN `api_datasets` ON `objects`.`dataset` = `api_datasets`.`base_alias`
      JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
        WHERE objects.unindexed='0' AND" .
//			`m_alerts`.`stream_id`='$stream_id' AND
            "`m_user-objects`.`user_id`='" . $user_id . "' AND" .
//			`m_user-objects`.`visited`='$visited' AND
            "`m_alerts-users`.`deleted`='0'";
//            if ($min)
//                $q .= " AND `m_user-objects`.`object_id`<'$min'";
        $q .= "GROUP BY `m_user-objects`.`object_id` ORDER BY $sql_order LIMIT $limit";

    } else {


//            if ($action == 'mark_all_as_read') {
//
//
//                $q = "UPDATE `m_user-objects`
//		  	JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
//		  	JOIN `m_alerts-users` ON `m_alerts-users`.`alert_id`=`m_alerts-objects`.`alert_id` AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
//		  	JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
//		  	SET `m_alerts-users`.`analiza`='1', `m_alerts-users`.`analiza_ts`=NOW(), `m_alerts-users`.`alerts_unread_count`='0', `m_user-objects`.`visited`='1', `m_user-objects`.`visited_ts`=NOW()
//		  	WHERE `m_alerts`.`stream_id`='$stream_id' AND `m_alerts-users`.`deleted`='0' AND `m_user-objects`.`user_id`='" . $this->USER['id'] . "' AND `m_user-objects`.`visited`='0' AND (`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $ids) . "')";
//                if ($action_max)
//                    $q .= " AND `m_user-objects`.`object_id`<='$action_max'";
//
//                $this->DB->q($q);
//                $this->S('me/alerts/count');
//
//
//            }


        $q = "SELECT $sql_fields
        FROM `m_user-objects`
        JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
        JOIN `m_alerts-users` ON `m_alerts-objects`.`alert_id`=`m_alerts-users`.alert_id AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
        JOIN `objects` ON `m_user-objects`.`object_id`=`objects`.`id`
        JOIN `api_datasets` ON `objects`.`dataset` = `api_datasets`.`base_alias`
      JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
        WHERE objects.unindexed='0' AND" .
//			`m_alerts`.`stream_id`='$stream_id' AND
            "`m_user-objects`.`user_id`='" . $user_id . "' AND " .
//			`m_user-objects`.`visited`='$visited' AND
            "(`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $ids) . "') AND `m_alerts-users`.`deleted`='0'";
//            if ($min)
//                $q .= " AND `m_user-objects`.`object_id`<'$min'";
        $q .= " GROUP BY `m_user-objects`.`object_id` ORDER BY $sql_order LIMIT $limit";

    }
    $objects = $this->Fraza->query($q);
    $dataobjects = array();
    $this->loadModel('Dane.Dataobject');
    foreach($objects as $object) {
        $obj = ($this->Dataobject->find('all',array(
            'conditions' => array(
                'dataset' => $object['objects']['dataset'],
                'object_id' => $object['objects']['object_id'],
            ),
        )));
        $obj['Dataobject'][0]['hl_text'] = $object['m_alerts-objects']['hl'];
        array_push($dataobjects, $obj);
    }
    $this->set(array(
            'objects' => $dataobjects,
            '_serialize' => array('objects'),
        )
    );
}

public function markread($user_id, $object_id)
{
    //@TODO : znalezc service na sejmo, ktory oznacz jedno powiadomienie jako przeczytane
}

public function delete($user_id, $phrase_id)
{
    $obj = $this->UzytkownikFraza->find('first', array(
        'conditions' => array(
            'UzytkownikFraza.user_id' => $user_id,
            'UzytkownikFraza.alert_id' => $phrase_id,
        ),
    ));
    if ($obj) {
        $this->UzytkownikFraza->id = $obj['UzytkownikFraza']['id'];
        $this->UzytkownikFraza->delete();
    }
    $this->set(array(
        'powiadomienia' => array(),
        '_serialize' => array('powiadomienia'),
    ));
}
*/
} 