<?php

class PowiadomieniaApp extends AppModel
{
    public $useTable = false;
    
    public function index() {
	    
	    App::import('model', 'DB');
	    $this->DB = new DB();
	    
	    
	    
	    $q = "SELECT 
	    `applications`.`id` AS 'app.id', 
	    `applications`.`name` AS 'app.name', 
	    `datasets`.`id` AS 'dataset.id', 
	    `datasets`.`name` AS 'dataset.name' 
	    FROM `datasets` 
	    JOIN `applications` ON `datasets`.`app_id` = `applications`.`id` 
	    WHERE `datasets`.`alerts` = '1' 
	    AND ( (`applications`.`public` = '1')";
	    
	    if( $user_id = $this->getCurrentUser('id') ) {
		    $q .= " OR (`applications`.`id` IN (SELECT `app_id` FROM `m_users-applications` WHERE `user_id`='" . addslashes( $user_id ) . "'))";
	    }
	    
	    $q .= " ) ORDER BY `applications`.`name` ASC, `datasets`.`name` ASC";
	    
	    $apps_data = $this->DB->selectAssocs($q);
	    	    
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
	    
	    return $apps;
	    
    }
    
} 