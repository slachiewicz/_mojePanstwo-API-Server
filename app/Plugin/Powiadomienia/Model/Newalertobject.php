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


        $sql_fields = "`m_users-objects`.`object_id`, `m_users-objects`.`cts`";
        $sql_order = "`m_users-objects`.`dstamp` DESC";

		
        if( $group_id )
        {

            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts-objects` ON `m_user-objects`.`dstamp`=`m_alerts-objects`.`dstamp` 
			WHERE `m_user-objects`.`user_id`='" . $user_id . "'";
			$q .= " AND `m_user-objects`.`visited`='" . $visited . "'";
            $q .= " GROUP BY `m_user-objects`.`dstamp`";
            $q .= " ORDER BY $sql_order";
            $q .= " LIMIT $offset, $limit";
			
			// TODO: objects.unindex, m_alerts-users.deleted

        }
        else
        {
            
            $q = "SELECT $sql_fields
            FROM `m_users-objects` ";
            
            // USE INDEX (`user_objects`) 
            
            $q .= "JOIN `m_alerts_groups-objects` ON `m_users-objects`.`dstamp`=`m_alerts_groups-objects`.`dstamp` 
			WHERE `m_users-objects`.`user_id`='" . $user_id . "'";
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
            
            if( isset($object['m_alerts-objects']['hl']) )
	            $hl_texts[$object['m_users-objects']['object_id']] = $object['m_alerts-objects']['hl'];
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