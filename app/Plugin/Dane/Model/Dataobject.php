<?

App::uses('Dataset', 'Dane.Model');
App::uses('Layer', 'Dane.Model');

class Dataobject extends AppModel
{
    public $useDbConfig = 'solr';
    public $id;
    public $data;

    public function setId($id)
    {

        return $this->id = $id;

    }

    public function find($type = 'first', $queryData = array())
    {

        /*
        $queryData = array_merge(array(
            'fields' => array('id', 'alias', 'name', 'count'),
            'order' => array('ord' => 'asc'),
            'limit' => 100,
        ), $queryData);
        */

        return parent::find($type, $queryData);

    }

    public function getObject($dataset, $id, $params = array(), $throw_not_found = false)
    {
        $data = $this->find('all', array(
            'conditions' => array(
                'dataset' => $dataset,
                'object_id' => $id,
            ),
            'limit' => 1,
        ));

        if (empty($data['dataobjects'])) {
            if ($throw_not_found) {
                throw new NotFoundException($dataset . ":" . $id);
            }
            return false;
        }
		
		$this->data = @$data['dataobjects'][0];

        // query dataset and its layers
        $mdataset = new Dataset();
        $ds= $mdataset->find('first', array(
            'conditions' => array(
                'Dataset.alias' => $dataset,
            ),
        ));

        $layers = array();
        foreach($ds['Layer'] as $layer) {
            $layers[$layer['layer']] = null;
        }
        $layers['dataset'] = null;

        // load queried layers
		if( isset($params['layers']) && !empty($params['layers']) ) {
            if ($params['layers'] == '*') {
                $params['layers'] = array_keys($layers);
            }
            if (!is_array($params['layers'])) {
                throw new BadRequestException("Invalid layers parameter: " . $params['layers']); // TODO unparse
            }

            foreach( $params['layers'] as $layer ) {
                if (!array_key_exists($layer, $layers)) {
                    // TODO dedicated 422 error
                    throw new BadRequestException("Layer doesn't exist: " . $layer);
                }

                if ($layer == 'dataset') {
                    $layers['dataset'] = $ds;
                } else {
                    $layers[$layer] = $this->getObjectLayer($dataset, $id, $layer);
                }
            }
        }

        $this->data['layers'] = $layers;

        return $this->data;
    }
    
    public function getRedirect($dataset, $id)
    {
		
		App::import('model', 'DB');
        $this->DB = new DB();
		
        switch( $dataset ) {
	        case 'zamowienia_publiczne': {
	        	
	        	if( $parent_id = $this->DB->selectValue("SELECT `parent_id` FROM `uzp_dokumenty` WHERE `id`='" . addslashes( $id ) . "' LIMIT 1") )
			        return array(
			        	'alias' => 'zamowienia_publiczne',
			        	'object_id' => $parent_id,
			        );
		        
	        }
	        
	        case 'zamowienia_publiczne_wykonawcy': {
	        	
	        	if( $krs_id = $this->DB->selectValue("SELECT `krs_id` FROM `uzp_wykonawcy` WHERE `id`='" . addslashes( $id ) . "' LIMIT 1") )
			        return array(
			        	'alias' => 'krs_podmioty',
			        	'object_id' => $krs_id,
			        );
		        
	        }
        }
        
        return false;

    }

    public function getObjectLayer($dataset, $id, $layer, $params = array())
    {
    	
    	$id = (int) $id;
    	
        $file = ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Dane' . DS . 'Model' . DS . 'Dataobject' . DS . $dataset . DS . 'layers' . DS . $layer . '.php';

        if (!file_exists($file))
            return false;

        App::import('model', 'DB');
        $this->DB = new DB();
        
        App::import('model', 'S3Files');
        $this->S3Files = new S3Files();

        $output = include($file);
        if ($layer == 'related') {

            if (@!empty($output['groups']))
                foreach ($output['groups'] as &$group) {

                    $objects = $group['objects'];
                    $search = $this->find('all', array(
                        'conditions' => array(
                            'objects' => $objects,
                        ),
                    ));

                    $search_objects = $search['dataobjects'];
                    $group['objects'] = array();

                    for ($i = 0; $i < count($objects); $i++) {

                        reset($search_objects);
                        foreach ($search_objects as &$search_object)
                            if (($search_object['dataset'] == $objects[$i]['dataset']) && ($search_object['object_id'] == $objects[$i]['object_id']))
                                $group['objects'][] = $search_object;

                    }

                }
        }
        return $output;
    }
    
    public function getAlertsQueries( $id, $user_id )
    {
	    
	    App::import('model', 'DB');
        $this->DB = new DB();
	    
	    $q = "SELECT `m_alerts_groups_qs-objects`.q_id, `m_alerts_qs`.`q` , `m_alerts_groups_qs-objects`.hl, COUNT( * ) AS `count`
		FROM `m_alerts_groups_qs-objects`
		JOIN `m_alerts_qs` ON `m_alerts_groups_qs-objects`.`q_id` = `m_alerts_qs`.`id`
		JOIN `m_alerts_groups-objects` ON `m_alerts_groups_qs-objects`.`object_id` = `m_alerts_groups-objects`.`object_id`
		JOIN `m_alerts_groups_qs` ON `m_alerts_groups-objects`.`group_id` = `m_alerts_groups_qs`.`group_id`
		WHERE `m_alerts_groups_qs-objects`.`object_id` = '" . $id . "'
		AND `m_alerts_groups-objects`.`user_id` = '" . $user_id . "'
		AND `m_alerts_groups_qs`.`q_id` = `m_alerts_groups_qs-objects`.q_id
		GROUP BY `m_alerts_groups_qs-objects`.hl
		ORDER BY `count` DESC , `m_alerts_qs`.`q` ASC
		LIMIT 0 , 30";
			    
	    return $this->DB->selectAssocs($q);
		
    }

}


