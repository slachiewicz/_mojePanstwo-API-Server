<?

class Dataobject extends AppModel
{

    public $useDbConfig = 'solr';
    public $id;

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

    public function getObject($dataset, $id)
    {

        $data = $this->find('all', array(
            'conditions' => array(
                'dataset' => $dataset,
                'object_id' => $id,
            ),
            'limit' => 1,
        ));

        return @$data['dataobjects'][0];

    }

    public function getObjectLayer($dataset, $id, $layer, $params = array())
    {
        $file = ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Dane' . DS . 'Model' . DS . 'Dataobject' . DS . $dataset . DS . 'layers' . DS . $layer . '.php';

        if (!file_exists($file))
            return false;

        App::import('model', 'DB');
        $this->DB = new DB();

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


