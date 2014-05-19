<?php

class PowiadomieniaGroup extends AppModel
{

	public $DB;
    public $useTable = 'm_alerts_groups';
    public $defaultFields = array('id', 'title', 'slug', 'user_id', 'alerts_unread_count');
    public $order = array("PowiadomieniaGroup.ord" => "asc");
    
    public $_schema = array(
	    'id',
	);  
    
    public $paginate = array(
        'limit' => 50,
    );
    
    public function __construct($id = false, $table = null, $ds = null)
    {
    	parent::__construct($id, $table, $ds);
    	
        App::import('model', 'DB');
	    $this->DB = new DB();
    }
    
    public function find($type = 'first', $query = array())
    {
	    if( !isset($query['fields']) || empty($query['fields']) )
	    	$query['fields'] = $this->defaultFields;
	    
	    if( $type=='first' )
	    {
		    
		    $output = parent::find($type, $query);
		    
		    if( empty($output) )
		    	return array(); // TODO: throw exception
		    
		    if( $id = $output['PowiadomieniaGroup']['id'] )
		    {
			    			    
			    $output['phrases'] = $this->DB->selectValues("SELECT `m_alerts_qs`.`q` 
			    FROM `m_alerts_groups_qs` 
			    JOIN `m_alerts_qs` ON `m_alerts_groups_qs`.`q_id` = `m_alerts_qs`.`id` 
			    WHERE `m_alerts_groups_qs`.`group_id` = '" . addslashes( $id ) . "' 
			    ORDER BY `m_alerts_qs`.`q` ASC 
			    LIMIT 50");
			    
			    $apps_data = $this->DB->selectAssocs("SELECT 
			    `applications`.`id` AS 'app.id', 
			    `applications`.`name` AS 'app.name', 
			    `datasets`.`id` AS 'dataset.id', 
			    `datasets`.`name` AS 'dataset.name' 
			    FROM `m_alerts_groups_datasets` 
			    JOIN `datasets` ON `m_alerts_groups_datasets`.`dataset_id` = `datasets`.`id` 
			    JOIN `applications` ON `datasets`.`app_id` = `applications`.`id` 
			    WHERE `m_alerts_groups_datasets`.`group_id` = '" . addslashes( $id ) . "' 
			    ORDER BY `applications`.`name` ASC, `datasets`.`name` ASC");
			    
			    $apps = array();
			    $temp = array();
			    foreach( $apps_data as $data )
			    	$temp[ $data['app.id'] ][] = $data;
			    
			    foreach( $temp as $app_id => $datasets )
			    {
			    	
			    	$app = array(
				    	'id' => $app_id,
				    	'name' => null,
				    	'datasets' => array(),
				    );
			    	
			    	foreach( $datasets as $dataset )
			    	{
			    		
			    		if( is_null($app['name']) )
			    			$app['name'] = $dataset['app.name'];
			    		
			    		$app['datasets'][] = array(
			    			'id' => $dataset['dataset.id'],
			    			'name' => $dataset['dataset.name'],
			    		);
			    	}
			    	
				    $apps[] = $app;
			    }
			    
			    $output['apps'] = $apps;
			    
		    }
		    
		    return $output;
		    
	    } else return parent::find($type, $query);	    
	    
    }
    
    public function delete()
    {
	    
	    if( !$this->id )
	    	return false;
	    	
	    echo "deleteting... " . $this->id;
	    die();
	    
    }
    
    public function save($data = null, $validate = true, $fieldList = array())
    {
	    	    
	    if( empty($this->data) )
	    	return false; // TODO: throw exception
	    	
	    $user_id = (int) $this->getCurrentUser('id');
	    if( !$user_id )
	    	return false; // TODO: throw exception
	    	    
	    $group = array(
	    	'title' => addslashes( $this->data['PowiadomieniaGroup']['title'] ),
	    	'slug' => addslashes( Inflector::slug($this->data['PowiadomieniaGroup']['title']) ),
	    	'type' => 'private',
	    	'user_id' => addslashes( $user_id ),
	    );
	    
	    	    
	    if( $this->id ) {
		    
		    if( !$this->DB->selectValue("SELECT COUNT(*) FROM `m_alerts_groups` WHERE `id`='". addslashes( $this->id ) ."' AND `user_id`='". addslashes( $user_id ) ."'") )
		    	return false; // TODO: throw exception
		    	
		    $this->DB->updateAssoc('m_alerts_groups', $group, $this->id);
		    
	    } else {
		    
		    $this->DB->insertIgnoreAssoc('m_alerts_groups', $group);
		    $this->id = $this->DB->getInsertID();		    
	    }
	    
	    
	    
	    
	    
	    // SAVING PHRASES
	    
	    if( isset($this->data['phrases']) ) {
		   	
		   	$phrases_ids = $this->getPhrasesIDs( $this->data['phrases'] );

		   	$this->DB->autocommit( false );
		   	$this->DB->q("DELETE FROM `m_alerts_groups_qs` WHERE `group_id`='" . addslashes( $this->id ) . "'");
		   	
		    
			foreach( $phrases_ids as $phrase_id )
				$this->DB->insertIgnoreAssoc('m_alerts_groups_qs', array(
					'group_id' => addslashes($this->id),
					'q_id' => addslashes($phrase_id),
				));
		    
	    }
	    
	    // SAVING APPS
	    
	    if( isset($this->data['apps']) ) {
		    		    
		    $this->DB->autocommit( false );
		   	$this->DB->q("UPDATE `m_alerts_groups_datasets` SET `deleted`='1' WHERE `group_id`='" . addslashes( $this->id ) . "' AND `filter_id`='0'");
		   	
		   	if( !empty($this->data['apps']) ) {
			   	foreach( $this->data['apps'] as $app ) {
			   		
				   	if( 
				   		( $app_id = $app['id'] ) && 
				   		isset($app['status']) && 
				   		$app['status'] && 
				   		( $app['status']!='false' )
				   	) {
				   		
				   		$dataset_ids = array();
				   		
				   		if( isset($app['datasets']) && !empty($app['datasets']) ) {
					   		
					   		foreach( $app['datasets'] as $d )
					   			$dataset_ids[] = $d['id'];
					   		
				   		} 
				   		
				   		/*else {
					   		
					   		$dataset_ids = $this->DB->selectValues("SELECT `id` FROM `datasets` WHERE `app_id`='" . addslashes( $app_id ) . "' AND `alerts`='1'");
					   		
				   		}
				   		*/
				   		
				   		$dataset_ids = array_filter($dataset_ids);
				   		$dataset_ids = array_unique($dataset_ids);
				   		

				   		
				   		if( !empty($dataset_ids) ) {
					   		foreach( $dataset_ids as $dataset_id ) {
						   		
						   		$group_dataset = array(
						   			'group_id' => addslashes( $this->id ),
						   			'dataset_id' => addslashes( $dataset_id ),
						   			'filter_id' => '0',
						   			'deleted' => '0',
						   		);
						   		
						   		$group_dataset_id = $this->DB->selectValue("SELECT `id` FROM `m_alerts_groups_datasets` WHERE `group_id`='" . $group_dataset['group_id'] . "' AND `dataset_id`='" . $group_dataset['dataset_id'] . "' AND `filter_id`='" . $group_dataset['filter_id'] . "'");
						   		
						   		if( $group_dataset_id )
						   			$this->DB->updateAssoc('m_alerts_groups_datasets', $group_dataset, $group_dataset_id);
						   		else
						   			$this->DB->insertIgnoreAssoc('m_alerts_groups_datasets', $group_dataset);
					   		
					   		}
				   		}				   		
				   	
				   	}
			   	}
		   	}
		   	
		   	$q = "DELETE FROM `m_alerts_groups_datasets` WHERE `deleted`='1' AND `group_id`='" . addslashes( $this->id ) . "' AND `filter_id`='0'";
		   	$this->DB->q($q);
		    
	    }  
	    
	    $this->DB->autocommit( true );
	    
	    return true;
	    
    }
    
    private function getPhrasesIDs($phrases){
	    
	    if( !is_array($phrases) || empty($phrases) )
	    	return array();
	    
	    foreach( $phrases as &$_phrase ) 
	    	$_phrase = addslashes( $_phrase );
	    
	    $this->DB->q('INSERT IGNORE INTO `m_alerts_qs` (`q`) VALUES ("' . implode('"), ("', $phrases) . '")');	    
	    $qs_map = $this->DB->selectDictionary('SELECT `q`, `id` FROM `m_alerts_qs` WHERE `q`="' . implode('" OR `q`="', $phrases) . '"');
	    
	    $output = array();
	    foreach( $phrases as $q )
		    if( $id = $qs_map[$q] )
		    	$output[] = (int) $id;
		    
		return $output;
	    
    }
    
    public function flag($group_id, $action)
    {
    
    	$user_id = (int) $this->getCurrentUser('id');
    	
	    if( !$user_id || !$group_id )
	    	return false;
	    	
	    if( $action!='read' && $action!='unread' )
	    	return false;
	    	
	    	
	    
	    
	    
	    $result = array(
			'status' => 'OK',
		);
	    
	    
	    	    
	    
	    if( $action=='read' ) {
	    	
	    	$sign = '-';
		    $this->DB->q("UPDATE `m_users-objects` JOIN `m_alerts_groups-objects` ON `m_users-objects`.`object_id` = `m_alerts_groups-objects`.`object_id` SET `m_users-objects`.`visited`='1', `m_users-objects`.`visited_ts`=NOW() WHERE `m_users-objects`.`user_id`='$user_id' AND `m_alerts_groups-objects`.`group_id`='$group_id' AND `m_users-objects`.`visited`='0'");
		
		} elseif( $action=='unread' ) {
			
			$sign = '+';
			$this->DB->q("UPDATE `m_users-objects` JOIN `m_alerts_groups-objects` ON `m_users-objects`.`object_id` = `m_alerts_groups-objects`.`object_id` SET `m_users-objects`.`visited`='0' WHERE `m_users-objects`.`user_id`='$user_id' AND `m_alerts_groups-objects`.`group_id`='$group_id' AND `m_users-objects`.`visited`='1'");
		
		}
	    
	    $affected_rows = $this->DB->getAffectedRows();
	    	 
		
		
		
		
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
    
    
} 