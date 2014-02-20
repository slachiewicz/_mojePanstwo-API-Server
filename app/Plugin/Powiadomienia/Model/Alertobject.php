<?php

class Alertobject extends AppModel
{

    public $useDbConfig = 'solr';
    public $objects = array();
    public $pagination = array();


    public function getObjects()
    {
        return $this->objects;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    public function find($type = 'first', $queryData = array())
    {
        $user_id = $queryData['conditions']['user_id'];
        $stream_id = $queryData['conditions']['stream_id'];
        $visited = $queryData['conditions']['visited'] ? '1' : '0';
        $keyword_id = $queryData['conditions']['keyword_id'];

        $offset = $queryData['offset'];
        $limit = $queryData['limit'];
//        CakeLog::debug(print_r($queryData, true));

        App::import('model', 'DB');
        $this->DB = new DB();


        $sql_fields = "`m_user-objects`.`object_id`, `m_alerts-objects`.`score`, `m_alerts-objects`.`hl`";
        $sql_order = "`m_user-objects`.`dstamp` DESC";

		
        if (empty($keyword_id)) {

            $q = "SELECT $sql_fields
            FROM `m_user-objects` 
            USE INDEX (`user_objects`) 
            JOIN `m_alerts-objects` ON `m_user-objects`.`dstamp`=`m_alerts-objects`.`dstamp` 
			WHERE `m_user-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_user-objects`.`visited`='" . $visited . "'";
			$q .= " AND `m_alerts-objects`.`stream_id`='" . $stream_id . "'";
            $q .= " GROUP BY `m_user-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";
			
			// TODO: objects.unindex, m_alerts-users.deleted

        } else {
            
            $q = "SELECT $sql_fields
            FROM `m_user-objects` 
            USE INDEX (`user_objects`) 
            JOIN `m_alerts-objects` ON `m_user-objects`.`dstamp`=`m_alerts-objects`.`dstamp` 
			WHERE `m_user-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_user-objects`.`visited`='" . $visited . "'";
			$q .= " AND `m_alerts-objects`.`stream_id`='" . $stream_id . "'";
			$q .= " AND (`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $keyword_id) . "')";
            $q .= " GROUP BY `m_user-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";         

        }
		
		
        $objects = $this->DB->query($q);
                
        


        $ids = array();
        $hl_texts = array();
        foreach ($objects as $i => $object) {
            array_push($ids, $object['m_user-objects']['object_id']);
            $hl_texts[$object['m_user-objects']['object_id']] = $object['m_alerts-objects']['hl'];
        }        


        if (!empty($ids)) {

            $data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
                'conditions' => array(
                    'id' => $ids,
                ),
                'order' => 'date desc',
            ));

            $dataobjects = $data['dataobjects'];
            foreach ($dataobjects as &$object)
            	if( array_key_exists($object['id'], $hl_texts) )
	                $object['hl_text'] = $hl_texts[$object['id']];
                        
        } else {
            $dataobjects = array();
        }


        $this->objects = $dataobjects;
        

        return array(
            'objects' => $this->getObjects(),
        );

    }
    
    public function flag($user_id, $object_id)
    {
	    if( !$user_id || !$object_id )
	    	return false;
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    $this->DB->query("INSERT LOW_PRIORITY INTO `m_users_history` (`user_id`, `object_id`) VALUES ('$user_id', '$object_id')");
	    $this->DB->query("UPDATE `m_user-objects` SET `m_user-objects`.`visited`='1', `m_user-objects`.`visited_ts`=NOW() WHERE `m_user-objects`.`user_id`='$user_id' AND `m_user-objects`.object_id='$object_id' AND `m_user-objects`.`visited`='0'");
	    
	    $affected_rows = $this->DB->getAffectedRows();
	    
	    
	    if( $affected_rows|| true )
			$this->DB->query( "UPDATE `m_alerts-users` JOIN `m_alerts-objects` ON `m_alerts-users`.`alert_id` = `m_alerts-objects`.`alert_id` SET `m_alerts-users`.analiza='1', `m_alerts-users`.analiza_ts=NOW() WHERE `m_alerts-objects`.`object_id`='$object_id'" );
			
		
		$result = array(
	    	'status' => $affected_rows,
	    );
		
		return $result;
		
    }
} 