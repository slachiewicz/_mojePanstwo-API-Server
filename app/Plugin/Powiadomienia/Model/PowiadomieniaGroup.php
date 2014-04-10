<?php

class PowiadomieniaGroup extends AppModel
{
    public $useTable = 'm_alerts_groups';
    
    public $defaultFields = array('id', 'title', 'slug', 'user_id', 'alerts_unread_count');
    
    public $order = array("PowiadomieniaGroup.ord" => "asc");
    
    public $_schema = array(
	    'id',
	);  
    
    // public $belongsTo = array('UserPhrase' => array('foreignKey' => 'alert_id', 'className' => 'Powiadomienia.UserPhrase'));
    
    public $paginate = array(
        'limit' => 50,
    );
    
    public function find($type = 'first', $query = array())
    {
	    if( !isset($query['fields']) || empty($query['fields']) )
	    	$query['fields'] = $this->defaultFields;
	    
	    return parent::find($type, $query);
	    
    }
    
    public function flag($user_id, $group_id, $action)
    {
	    if( !$user_id || !$group_id )
	    	return false;
	    	
	    if( $action!='read' && $action!='unread' )
	    	return false;
	    	
	    	
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    
	    
	    $result = array(
			'status' => 'OK',
		);
	    
	    
	    	    
	    
	    if( $action=='read' ) {
	    	
	    	$sign = '-';
		    $this->DB->query("UPDATE `m_users-objects` JOIN `m_alerts_groups-objects` ON `m_users-objects`.`object_id` = `m_alerts_groups-objects`.`object_id` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_alerts_groups-objects`.`group_id`='$group_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
			
			$sign = '+';
			$this->DB->query("UPDATE `m_users-objects` JOIN `m_alerts_groups-objects` ON `m_users-objects`.`object_id` = `m_alerts_groups-objects`.`object_id` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_alerts_groups-objects`.`group_id`='$group_id' AND `m_users-objects`.`visited`='1'");
		
		}
	    
	    $affected_rows = $this->DB->getAffectedRows();
	    	 
		
		
		
		
		if( $affected_rows || true ) {
			
			$groups = $this->DB->selectAssocs("SELECT `m_alerts_groups-objects`.`group_id`, COUNT(*) as 'alerts_unread_count' FROM `m_alerts_groups-objects` JOIN `m_users-objects` ON (`m_alerts_groups-objects`.`object_id` = `m_users-objects`.`object_id` AND `m_alerts_groups-objects`.`user_id`=`m_users-objects`.`user_id`) WHERE `m_alerts_groups-objects`.`user_id`='" . $user_id . "' AND `m_users-objects`.`visited`='0' GROUP BY `m_alerts_groups-objects`.`group_id`");
			
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