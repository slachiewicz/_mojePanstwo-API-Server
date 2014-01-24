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

}


