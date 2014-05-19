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
    	    	
        $user_id = $this->getCurrentUser('id');
        $visited = $queryData['conditions']['visited'] ? '1' : '0';
        $group_id = $queryData['conditions']['group_id'];

        $offset = $queryData['offset'];
        $limit = $queryData['limit'];

        App::import('model', 'DB');
        $this->DB = new DB();


        $sql_fields = "`m_users-objects`.`object_id`, `m_users-objects`.`cts`,  GROUP_CONCAT( DISTINCT(`m_alerts_groups_qs-objects`.`hl`) SEPARATOR \"\r\") as 'hls' ";
        $sql_order = "`m_users-objects`.`dstamp` DESC";
		
				
        if( $group_id )
        {

            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts_groups` 
			ON (`m_users-objects`.`user_id`=`m_alerts_groups`.`user_id` AND `m_alerts_groups`.`id`='" . $group_id . "') 
			
			JOIN `m_alerts_groups-objects` 
			ON (`m_users-objects`.`dstamp`=`m_alerts_groups-objects`.`dstamp` AND `m_alerts_groups-objects`.`group_id`='" . $group_id . "') 
			
			JOIN `m_alerts_groups_qs-objects` 
			ON (`m_users-objects`.`dstamp`=`m_alerts_groups_qs-objects`.`dstamp`) 
			
			JOIN `m_alerts_groups_qs` 
			ON (`m_alerts_groups_qs`.`q_id`=`m_alerts_groups_qs-objects`.`q_id` AND `m_alerts_groups_qs`.`group_id`='" . $group_id . "')
			
			";
            
            $q .= " WHERE `m_users-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_users-objects`.`visited`='" . $visited . "'";
            $q .= " GROUP BY `m_users-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";
			
			// TODO: objects.unindex

        }
        else
        {
            
            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts_groups` 
			ON (`m_users-objects`.`user_id`=`m_alerts_groups`.`user_id`) 
			
			JOIN `m_alerts_groups-objects` 
			ON (`m_users-objects`.`dstamp`=`m_alerts_groups-objects`.`dstamp`) 
			
			JOIN `m_alerts_groups_qs-objects` 
			ON (`m_users-objects`.`dstamp`=`m_alerts_groups_qs-objects`.`dstamp`) 
			
			JOIN `m_alerts_groups_qs` 
			ON (`m_alerts_groups_qs`.`q_id`=`m_alerts_groups_qs-objects`.`q_id` AND `m_alerts_groups_qs`.`group_id`=`m_alerts_groups`.`id`)";
			
			

            $q .= "WHERE `m_users-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_users-objects`.`visited`='" . $visited . "'";
            $q .= " GROUP BY `m_users-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";         			 
						
        }
		
        $objects = $this->DB->selectAssocs($q);
                

        $ids = array();
        $hl_texts = array();
        foreach ($objects as $object) {
            
            $ids[] = $object['object_id'];
            
            if( isset($object['hls']) && ($object['hls'] = trim($object['hls'])) )
	            $hl_texts[ $object['object_id'] ] = $object['hls'];
        }        
		
		
        if (!empty($ids)) {
						
            $data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
                'conditions' => array(
                    'id' => $ids,
                ),
                'order' => 'date desc',
            ));
            
            $dataobjects = $data['dataobjects'];
            
            foreach ($dataobjects as &$object) {
            
            	if( array_key_exists($object['id'], $hl_texts) )
	                $object['hl_text'] = $hl_texts[$object['id']];
	            else
	            	$object['hl_text'] = false;
	        
	        }
                        
        } else {
            $dataobjects = array();
        }

		
		
        $this->objects = $dataobjects;
        

        return array(
            'objects' => $this->getObjects(),
        );

    }
    
    public function flag($object_id, $action)
    {
    	    	
    	$user_id = $this->getCurrentUser('id');
    	
	    if( !$user_id || !$object_id )
	    	return false;
	    	
	    if( $action!='read' && $action!='unread' )
	    	return false;
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    
	    $this->DB->q("INSERT LOW_PRIORITY INTO `m_users_history` (`user_id`, `object_id`) VALUES ('$user_id', '$object_id')");
	    	    
	    
	    if( $action=='read' ) {
	    	
		    $this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
			
			$this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='1'");
		
		}
		
		$affected_rows = $this->DB->_getAffectedRows();
		
		$result = array(
			'status' => 'OK',
		);
		
		if( $affected_rows )
			$result = array_merge($result, $this->calculate($object_id));			
		
		return $result;
		
    }
    
    public function flagAll($action)
    {
	    
	    $user_id = $this->getCurrentUser('id');
	    
	    if( $action!='read' && $action!='unread' )
	    	return false;
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    
	    	    
	    
	    if( $action=='read' ) {
	    	
		    $this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
			
			$this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.`visited`='1'");
		
		}
		
		$affected_rows = $this->DB->_getAffectedRows();
		
		
		$result = array(
			'status' => 'OK',
		);
		
		if( $affected_rows || true ) {
			
			
			$groups = $this->DB->selectAssocs("SELECT `m_alerts_groups`.`id` as 'group_id', '0' as 'alerts_unread_count' FROM `m_alerts_groups` WHERE `m_alerts_groups`.`user_id`='" . $user_id . "'");
			
			if( !empty($groups) ) {
				
				$values = array();
				foreach( $groups as $group )
					$values[] = "('" . $group['group_id'] . "', '" . $group['alerts_unread_count'] . "')";
				
				
				$this->DB->q("INSERT INTO `m_alerts_groups` (`id`, `alerts_unread_count`) VALUES " . implode(',', $values) . " ON DUPLICATE KEY UPDATE `alerts_unread_count`=VALUES(`alerts_unread_count`)");
				
			}
			
			$user_alerts_count = (int) $this->DB->selectValue("SELECT COUNT(*) FROM `m_users-objects` WHERE `user_id` = '$user_id' AND visited='0'");
			$this->DB->q("UPDATE `m_users` SET `alerts_unread_count`='$user_alerts_count' WHERE `id`='$user_id'");
			
			$result = array_merge($result, array(
				'groups_alerts_counts' => $groups,
		    	'user_alerts_count' => $user_alerts_count,
			));
			
			
			
		}
			
		
		
		return $result;
		
    }
    
    private function calculate( $object_id ) {
						
		$user_id = $this->getCurrentUser('id');
		
	    $q = "SELECT `m_alerts_groups-objects`.`group_id`, COUNT(*) as 'alerts_unread_count' FROM `m_alerts_groups-objects` JOIN `m_users-objects` ON (`m_alerts_groups-objects`.`object_id` = `m_users-objects`.`object_id` AND `m_alerts_groups-objects`.`user_id`=`m_users-objects`.`user_id`) WHERE `m_alerts_groups-objects`.`user_id`='" . $user_id . "' AND `m_users-objects`.`visited`='0' AND `m_alerts_groups-objects`.`group_id` IN (SELECT DISTINCT(`group_id`) FROM `m_alerts_groups-objects` WHERE `user_id`='" . $user_id . "' AND `object_id`='" . $object_id . "') GROUP BY `m_alerts_groups-objects`.`group_id`";
	    	    
	    $groups = $this->DB->selectAssocs($q);
	    
		if( !empty($groups) ) {
			
			foreach( $groups as $group )
				$values[] = "('" . $group['group_id'] . "', '" . $group['alerts_unread_count'] . "')";
			
			$this->DB->q("INSERT INTO `m_alerts_groups` (`id`, `alerts_unread_count`) VALUES " . implode(',', $values) . " ON DUPLICATE KEY UPDATE `alerts_unread_count`=VALUES(`alerts_unread_count`)");
			
		}
		
		$user_alerts_count = (int) $this->DB->selectValue("SELECT COUNT(*) FROM `m_users-objects` WHERE `user_id` = '$user_id' AND visited='0'");
		$this->DB->q("UPDATE `m_users` SET `alerts_unread_count`='$user_alerts_count' WHERE `id`='$user_id'");
		
		$result = array(
			'groups_alerts_counts' => $groups,
	    	'user_alerts_count' => $user_alerts_count,
		);
		
		return $result;
	    
    }
    
} 