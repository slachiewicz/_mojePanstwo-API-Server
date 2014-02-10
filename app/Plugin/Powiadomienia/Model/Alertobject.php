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


        $sql_fields = "`m_alerts-objects`.`object_id` as '_object_id', `m_alerts-objects`.`score`, `m_alerts-objects`.`hl`, `objects`.`dataset`, `objects`.`object_id`, `api_datasets`.`results_class`";
        $sql_order = "`objects`.`date` DESC, `m_alerts-objects`.`object_id` DESC";

		
        if (empty($keyword_id)) {

            $q = "SELECT SQL_CALC_FOUND_ROWS $sql_fields
            FROM `m_user-objects`
            JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
            JOIN `m_alerts-users` ON `m_alerts-objects`.`alert_id`=`m_alerts-users`.alert_id AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
            JOIN `objects` ON `m_user-objects`.`object_id`=`objects`.`id`
            JOIN `api_datasets` ON `objects`.`dataset_id` = `api_datasets`.`id`
            JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
            WHERE objects.unindexed='0' AND " .
                "`m_alerts` . `stream_id` = '$stream_id' AND " .
                "`m_user-objects`.`user_id`='" . $user_id . "' AND " .
                "`m_user-objects`.`visited`='$visited' AND " .
                "`m_alerts-users`.`deleted`='0'";
//          if ($min)
//                $q .= " AND `m_user-objects`.`object_id`<'$min'";
            $q .= "GROUP BY `m_user-objects`.`object_id` ORDER BY $sql_order LIMIT $offset, $limit";


        } else {
            $q = "SELECT SQL_CALC_FOUND_ROWS $sql_fields
            FROM `m_user-objects`
            JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id`
            JOIN `m_alerts-users` ON `m_alerts-objects`.`alert_id`=`m_alerts-users`.alert_id AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id`
            JOIN `objects` ON `m_user-objects`.`object_id`=`objects`.`id`
            JOIN `api_datasets` ON `objects`.`dataset_id` = `api_datasets`.`id`
            JOIN `m_alerts` ON `m_alerts-objects`.`alert_id`=`m_alerts`.`id`
            WHERE objects.unindexed='0' AND " .
                "`m_alerts` . `stream_id` = '$stream_id' AND " .
                "`m_user-objects`.`user_id`='" . $user_id . "' AND " .
                "`m_user-objects`.`visited`='$visited' AND " .
                "(`m_alerts-objects`.`alert_id`='" . implode("' OR `m_alerts-objects`.`alert_id`='", $keyword_id) . "') AND `m_alerts-users`.`deleted`='0'";
//          if ($min)
//              $q .= " AND `m_user-objects`.`object_id`<'$min'";
            $q .= " GROUP BY `m_user-objects`.`object_id` ORDER BY $sql_order LIMIT $offset, $limit";
            

        }


        $data = $this->DB->queryCount($q);
                
        $objects = $data[0];
        $count = $data[1];
        unset($data);


        $ids = array();
        $hl_texts = array();
        foreach ($objects as $i => $object) {
            array_push($ids, $object['m_alerts-objects']['_object_id']);
            $hl_texts[$object['m_alerts-objects']['_object_id']] = $object['m_alerts-objects']['hl'];
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
            $count = 0;
            $dataobjects = array();
        }


        $this->objects = $dataobjects;
        $this->pagination = array(
            'total' => $count,
        );

        return array(
            'objects' => $this->getObjects(),
            'pagination' => $this->getPagination(),
        );

    }
    
    public function flag($user_id, $object_id)
    {
	    if( !$user_id || !$object_id )
	    	return false;
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    $this->DB->query("INSERT INTO `m_users_history` (`user_id`, `object_id`) VALUES ('$user_id', '$object_id')");
	    $this->DB->query("UPDATE `m_user-objects` SET `visited`='1', `visited_ts`=NOW() WHERE `user_id`='$user_id' AND object_id='$object_id' AND `visited`='0'");
	    
	    if( $affected_rows = $this->DB->getAffectedRows() )
	    {
		    
	    
			$total_unread_count = $this->DB->selectValue("SELECT COUNT(DISTINCT(`m_alerts-objects`.`object_id`)) FROM `m_user-objects` JOIN `m_alerts-objects` ON `m_user-objects`.`object_id`=`m_alerts-objects`.`object_id` JOIN `m_alerts-users` ON `m_alerts-objects`.`alert_id`=`m_alerts-users`.alert_id AND `m_user-objects`.`user_id`=`m_alerts-users`.`user_id` JOIN `objects` ON `m_user-objects`.`object_id`=`objects`.`id` JOIN `api_datasets` ON `objects`.`dataset` = `api_datasets`.`base_alias` WHERE `m_user-objects`.`user_id`='" . $user_id . "' AND `m_user-objects`.`visited`='0' AND `m_alerts-users`.`deleted`='0'");
			
			$q = "SELECT `m_alerts-users`.id, `m_user-objects`.`visited`, COUNT(*) as 'count'
	FROM `m_user-objects`
	JOIN `m_alerts-objects` ON `m_user-objects`.`object_id` = `m_alerts-objects`.`object_id`
	JOIN `m_alerts-users` ON `m_alerts-users`.`alert_id` = `m_alerts-objects`.`alert_id`
	AND `m_alerts-users`.`user_id` = `m_user-objects`.`user_id`
	WHERE `m_user-objects`.`user_id` = '$user_id'
	AND `m_user-objects`.`deleted` = '0'
	AND (`m_user-objects`.`visited` = '0' OR `m_user-objects`.`visited` = '1')
	GROUP BY `m_alerts-objects`.alert_id, `m_user-objects`.`visited`";
			$data = $this->DB->selectAssocs( $q );
			
	
			
			// $this->DB->autocommit(false);
			$q = "UPDATE `m_alerts-users` SET `alerts_unread_count`=0, `alerts_read_count`=0 WHERE `user_id`='$user_id'";
			$this->DB->query($q);
			
			foreach( $data as $d ) {
				
				$column = false;
				if( $d['visited']=='0' )
					$column = 'alerts_unread_count';
				if( $d['visited']=='1' )
					$column = 'alerts_read_count';
					
				if( $column )
				{
					$q = "UPDATE `m_alerts-users` SET `$column`='" . $d['count'] . "' WHERE id='" . $d['id'] . "'";
					$this->DB->query($q);
				}
				
			}
			
			$this->DB->query("UPDATE `m_users` SET `alerts_unread_count`='$total_unread_count' WHERE `id`='$user_id'");		
			// $this->DB->autocommit(true);
		
		}
		
		$result = array(
	    	'status' => $affected_rows,
	    );
		
		return $result;
		
    }
} 