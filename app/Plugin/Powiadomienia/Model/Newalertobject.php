<?php

class Newalertobject extends AppModel
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
        $visited = $queryData['conditions']['visited'] ? '1' : '0';
        $group_id = $queryData['conditions']['group_id'];

        $offset = $queryData['offset'];
        $limit = $queryData['limit'];

        App::import('model', 'DB');
        $this->DB = new DB();


        $sql_fields = "`m_users-objects`.`object_id`, `m_users-objects`.`cts`,  GROUP_CONCAT( `m_alerts_groups_qs-objects`.`hl` SEPARATOR \"\r\") as 'hls' ";
        $sql_order = "`m_users-objects`.`dstamp` DESC";
		
				
        if( $group_id )
        {

            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts_groups-objects` ON `m_users-objects`.`dstamp`=`m_alerts_groups-objects`.`dstamp` ";
            
	        $q .= "LEFT JOIN `m_alerts_groups_qs` ON `m_alerts_groups-objects`.`group_id`=`m_alerts_groups_qs`.`group_id` ";
	        $q .= "LEFT JOIN `m_alerts_groups_qs-objects` ON (`m_users-objects`.`dstamp`=`m_alerts_groups_qs-objects`.`dstamp` AND `m_alerts_groups_qs-objects`.`q_id` = `m_alerts_groups_qs`.`q_id`) ";
            
            $q .= "WHERE `m_users-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_alerts_groups-objects`.`group_id`='" . $group_id . "'";
			$q .= " AND `m_users-objects`.`visited`='" . $visited . "'";
            $q .= " GROUP BY `m_users-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";
			
			// TODO: objects.unindex, m_alerts-users.deleted

        }
        else
        {
            
            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts_groups-objects` ON `m_users-objects`.`dstamp`=`m_alerts_groups-objects`.`dstamp` ";
            
	        $q .= "LEFT JOIN `m_alerts_groups_qs` ON `m_alerts_groups-objects`.`group_id`=`m_alerts_groups_qs`.`group_id` ";
	        $q .= "LEFT JOIN `m_alerts_groups_qs-objects` ON (`m_users-objects`.`dstamp`=`m_alerts_groups_qs-objects`.`dstamp` AND `m_alerts_groups_qs-objects`.`q_id` = `m_alerts_groups_qs`.`q_id`) ";
            
            $q .= "WHERE `m_users-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_users-objects`.`visited`='" . $visited . "'";
            $q .= " GROUP BY `m_users-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";         			 
			
        }
		
        $objects = $this->DB->query($q);
                


        $ids = array();
        $hl_texts = array();
        foreach ($objects as $i => $object) {
            array_push($ids, $object['m_users-objects']['object_id']);
            
            if( isset($object[0]['hls']) )
	            $hl_texts[$object['m_users-objects']['object_id']] = $object[0]['hls'];
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
    
    public function flag($user_id, $object_id, $action)
    {
	    if( !$user_id || !$object_id )
	    	return false;
	    	
	    if( $action!='read' && $action!='unread' )
	    	return false;
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    
	    $this->DB->query("INSERT LOW_PRIORITY INTO `m_users_history` (`user_id`, `object_id`) VALUES ('$user_id', '$object_id')");
	    	    
	    
	    if( $action=='read' ) {
	    	
		    $this->DB->query("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
			
			$this->DB->query("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='0'");
		
		}
		
		$affected_rows = $this->DB->getAffectedRows();
		
		
		$result = array(
			'status' => 'OK',
		);
		
		if( $affected_rows || true ) {
			
			
			$groups = $this->DB->selectAssocs("SELECT `m_alerts_groups-objects`.`group_id`, COUNT(*) as 'alerts_unread_count' FROM `m_alerts_groups-objects` WHERE `m_alerts_groups-objects`.`user_id`='" . $user_id . "' AND `m_alerts_groups-objects`.`object_id`='" . $object_id . "' GROUP BY `m_alerts_groups-objects`.`group_id`");
			
			if( !empty($groups) ) {
				
				$values = array("('" . $group_id . "','0')");
				foreach( $groups as $group )
					$values[] = "('" . $group['group_id'] . "', '" . $group['alerts_unread_count'] . "')";
				
				
				$this->DB->query("INSERT INTO `m_alerts_groups` (`id`, `alerts_unread_count`) VALUES " . implode(',', $values) . " ON DUPLICATE KEY UPDATE `alerts_unread_count`=VALUES(`alerts_unread_count`)");
				
			}
			
			$user_alerts_count = (int) $this->DB->selectValue("SELECT COUNT(*) FROM `m_users-objects` WHERE `user_id` = '$user_id' AND visited='0'");
			$this->DB->query("UPDATE `m_users` SET `alerts_unread_count`='$user_alerts_count' WHERE `id`='$user_id'");
			
			$groups[] = array(
				'group_id' => $group_id,
				'alerts_unread_count' => 0,
			);
			
			$result = array_merge($result, array(
				'groups_alerts_counts' => $groups,
		    	'user_alerts_count' => $user_alerts_count,
			));
			
			
			
		}
			
		
		
		return $result;
		
    }
} 