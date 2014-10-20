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
    	    	
        if( $user_id = $this->getCurrentUser('id') ) {
        	        	
	    	// Configure::write('debug', 2);

	        $visited = ( isset($queryData['conditions']['visited']) && $queryData['conditions']['visited'] ) ? '1' : '0';
	        $group_id = isset( $queryData['conditions']['group_id'] ) ? $queryData['conditions']['group_id'] : false;
	
	        $offset = isset( $queryData['offset'] ) ? $queryData['offset'] : 0;
	        $limit = isset( $queryData['limit'] ) ? $queryData['limit'] : 20;
	        $page = isset( $queryData['page'] ) ? $queryData['page'] : 1;
	
	        
			App::import('model', 'DB');
	        $this->DB = new DB();
			
			/*			
			$q = "SELECT `id` FROM `m_alerts_groups` WHERE `user_id`='" . $this->getCurrentUser('id') . "'";
			if( $group_id )
				$q .= " AND ``='" . addslashes( $group_id ) . "'";
			*/
			
			
			
			
			$alerts_groups = $this->DB->selectAssocs("SELECT `id`, `title` FROM `m_alerts_groups` WHERE `user_id`='" . $this->getCurrentUser('id') . "' ORDER BY `title` ASC");
        	$alerts_gropu_ids = array_column($alerts_groups, 'id');
			
			if( $group_id && !in_array($group_id, $alerts_gropu_ids) )
				$group_id = false;
			
			
			App::import('model', 'Dane.Dataobject');
	        $this->Dataobject = new Dataobject();
			// ,
			
			$search = $this->Dataobject->find('all', array(
	        	// 'q' => $q,
	        	'mode' => 'search_main',
	        	'filters' => array(
	        		'_source' => 'alerts:' . $this->getCurrentUser('id') . '|' . $group_id . '|' . $visited,
	        	),
	        	'facets' => array(
	    			array('alerts', '(' . implode('|', $alerts_gropu_ids) . ')'),
	        	),
	        	// 'order' => $order,
	        	'limit' => $limit,
	        	'page' => $page,
	        	'version' => 'v3',
	        ));
			

			
			foreach( $alerts_groups as &$group ) {
				
				$group['alerts_unread_count'] = 0;
				
				for( $_i=0; $_i<count($search['facets']['alerts'][0]); $_i++ ) {
					if( $search['facets']['alerts'][0][$_i]['key'] == $group['id'] ) {
						$group['alerts_unread_count'] = $search['facets']['alerts'][0][$_i]['doc_count'];
						break;
					}
				}
				
			}
			
			
			$search['facets']['alerts'] = $alerts_groups;	
			return $search;		
		
		}
		
		/*
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
        
        */

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
	    
	    App::Import('ConnectionManager');
		$MPSearch = ConnectionManager::getDataSource('MPSearch');
	    
	    // $this->DB->q("INSERT LOW_PRIORITY INTO `m_users_history` (`user_id`, `object_id`) VALUES ('$user_id', '$object_id')");
	    
	    
	    $dataset = $this->DB->selectValue("SELECT dataset FROM objects WHERE id='$object_id'");	    
	    	    
	    if( $action=='read' ) {
	    	
	    	$this->DB->insertUpdateAssoc('m_users-objects', array(
	    		'user_id' => $user_id,
	    		'object_id' => $object_id,
	    		'visited' => '1',
	    		'visited_ts' => 'NOW()',
	    	));
	    	
	    	$ret = $MPSearch->API->update(array(
	    		'index' => 'objects_v2_01',
			  	'type' => 'alerts_' . $dataset,
			  	'id' => $object_id . '-' . $user_id,
			  	'parent' => $object_id,
			  	'body' => array(
				  	'script' => 'ctx._source.read = status',
				    'params' => array(
				        'status' => true,
				    ),
				),
	    	));
	    	
	    	// debug( $ret );
	    	
		    // $this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
						
			// $this->DB->q("UPDATE `m_users-objects` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_users-objects`.object_id='$object_id' AND `m_users-objects`.`visited`='1'");
			
			$this->DB->insertUpdateAssoc('m_users-objects', array(
	    		'user_id' => $user_id,
	    		'object_id' => $object_id,
	    		'visited' => '0',
	    		'visited_ts' => 'NOW()',
	    	));
	    	
	    	$ret = $MPSearch->API->update(array(
	    		'index' => 'objects_v2_01',
			  	'type' => 'alerts_' . $dataset,
			  	'id' => $object_id . '-' . $user_id,
			  	'parent' => $object_id,
			  	'body' => array(
				  	'script' => 'ctx._source.read = status',
				    'params' => array(
				        'status' => false,
				    ),
				),
	    	));
		
		}
		
		$affected_rows = $this->DB->_getAffectedRows();
		
		$result = array(
			'status' => 'OK',
		);
		
		/*
		if( $affected_rows )
			$result = array_merge($result, $this->calculate($object_id));			
		*/
		
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
		
		/*
		if( $affected_rows || true )			
			$result = array_merge($result, $this->calculate($group_id));			
		*/
		
		return $result;
		
    }
    
    private function calculate( $object_id = false ) {
						
		$user_id = $this->getCurrentUser('id');
		$object_id = (int) $object_id;
		
		if( $object_id ) {
		
		    $q = "SELECT `m_alerts_groups-objects`.`group_id`, COUNT(*) as 'alerts_unread_count' FROM `m_alerts_groups-objects` JOIN `m_users-objects` ON (`m_alerts_groups-objects`.`object_id` = `m_users-objects`.`object_id` AND `m_alerts_groups-objects`.`user_id`=`m_users-objects`.`user_id`) WHERE `m_alerts_groups-objects`.`user_id`='" . $user_id . "' AND `m_users-objects`.`visited`='0' AND `m_users-objects`.`archived`='0' AND `m_alerts_groups-objects`.`group_id` IN (SELECT DISTINCT(`group_id`) FROM `m_alerts_groups-objects` WHERE `user_id`='" . $user_id . "' AND `object_id`='" . $object_id . "') GROUP BY `m_alerts_groups-objects`.`group_id`";
		    
		} else {
			
			$q = "SELECT `m_alerts_groups-objects`.`group_id`, COUNT(*) as 'alerts_unread_count' FROM `m_alerts_groups-objects` JOIN `m_users-objects` ON (`m_alerts_groups-objects`.`object_id` = `m_users-objects`.`object_id` AND `m_alerts_groups-objects`.`user_id`=`m_users-objects`.`user_id`) WHERE `m_alerts_groups-objects`.`user_id`='" . $user_id . "' AND `m_users-objects`.`visited`='0' AND `m_users-objects`.`archived`='0' GROUP BY `m_alerts_groups-objects`.`group_id`";
			
		}
	    	    
	    $groups = $this->DB->selectAssocs($q);
	    
		if( !empty($groups) ) {
			
			foreach( $groups as $group )
				$values[] = "('" . $group['group_id'] . "', '" . $group['alerts_unread_count'] . "')";
			
			$this->DB->q("INSERT INTO `m_alerts_groups` (`id`, `alerts_unread_count`) VALUES " . implode(',', $values) . " ON DUPLICATE KEY UPDATE `alerts_unread_count`=VALUES(`alerts_unread_count`)");
			
		}
		
		$user_alerts_count = (int) $this->DB->selectValue("SELECT COUNT(*) FROM `m_users-objects` WHERE `user_id` = '$user_id' AND visited='0' AND `archived`='0'");
		$this->DB->q("UPDATE `m_users` SET `alerts_unread_count`='$user_alerts_count' WHERE `id`='$user_id'");
		
		$result = array(
			'groups_alerts_counts' => $groups,
	    	'user_alerts_count' => $user_alerts_count,
		);
		
		return $result;
	    
    }
    
} 