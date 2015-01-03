<?php

class SystemController extends AppController
{

    public function health()
    {
    	
    	App::Import('ConnectionManager');
		$MPSearch = ConnectionManager::getDataSource('MPSearch');
        $elasticSearch = $MPSearch->API->cluster()->health();

        $this->set(array(
            'elasticSearch' => $elasticSearch,
            '_serialize' => array('elasticSearch'),
        ));		
		
    }


} 