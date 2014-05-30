<?

class Dataset extends AppModel
{

    public $useTable = 'datasets';
    public $belongsTo = array(
        'Datachannel' => array(
            'className' => 'Dane.Datachannel',
            'foreignKey' => 'channel_id',
        ),

    );
    public $actsAs = array('Containable');
    public $virtualFields = array(
        'alias' => 'base_alias',
        'class' => 'SUBSTRING(results_class, 4)',
    );

    public function find($type = 'first', $queryData = array())
    {

        $fields = array();
        if (isset($queryData['fields']))
            $fields = $queryData['fields'];
        else {
            $fields = array(
                'Dataset.id',
                'Dataset.alias',
                'Dataset.name',
                'Dataset.class',
                'Dataset.count',
                'Dataset.channel_id',
                'Dataset.default_sort',
                'Datachannel.id',
                'Datachannel.nazwa',
                'Datachannel.slug',
            );
        }

        $queryData = array_merge_recursive(array(
            'fields' => $fields,
            'order' => array('Dataset.ord' => 'asc'),
            'limit' => 100,
        ), $queryData);

        return parent::find($type, $queryData);
    }

    public function getFilters($alias, $full = true, $exclude_alias = null)
    {

        $fields = array('`filter`.`field`', '`filter`.`typ_id`');

        if ($full)
            $fields = array_merge($fields, array('`filter`.`parent_field`', '`filter`.`label`', '`filter`.`desc`'));


        // $fq = "CONCAT( `field`.`alias` , '.', `field`.`field` )";
        $q = "SELECT " . implode(", ", $fields) . " 
        FROM `datasets_filters` AS filter 
        WHERE `filter`.`dataset`='" . addslashes($alias) . "'";

        $q .= " AND `filter`.`deleted`='0'";

        $q .= " ORDER BY `filter`.`ord` ASC 
        LIMIT 100";

        $result = $this->query($q);

        if (!is_null($exclude_alias)) {
            $exclude = $this->query("SELECT field FROM datasets_filters_datasets WHERE dataset = '$alias' and perspective = '$exclude_alias'");
            $excludefields = array();

            foreach ($exclude as $field)
                array_push($excludefields, $field['datasets_filters_datasets']['field']);

            foreach ($result as $key => $val)
                if (in_array($val['filter']['field'], $excludefields))
                    unset($result[$key]);
        }


        return $result;

    }

    public function getSwitchers($alias, $full = true, $exclude = null)
    {

        $fields = array('`name`', '`label`', '`dataset_search_default`');

        if ($full)
            $fields = array_merge($fields, array('`expression`'));

        return $this->query("SELECT " . implode(", ", $fields) . " 
	    	FROM `datasets_switchers` AS `switcher`
	    	WHERE `dataset`='" . addslashes($alias) . "'
	    	ORDER BY `ord` ASC 
	    	LIMIT 100");

    }
    
    public function getMap($alias, $page)
    {
		
		$dataset = $this->query("SELECT `id` FROM `datasets` WHERE `base_alias`='" . addslashes( $alias ) . "' LIMIT 1");
		$dataset_id = $dataset[0]['datasets']['id'];
			
		$offset = ($page-1) * 50000;
			
        $items = $this->query("SELECT object_id 
	    	FROM `objects`
	    	WHERE `dataset_id`='" . $dataset_id . "'
	    	AND `a`='3'
	    	ORDER BY `object_id` DESC 
	    	LIMIT $offset, 50000");
	    	
	    $output = array();
	    foreach( $items as $item )
	    	$output[] = $item['objects']['object_id'];
	    
	    return $output;
	    
    }

    public function getSortings($alias)
    {

        return $this->query("SELECT `field`, `label`, `direction`
	    	FROM `datasets_orders` AS sorting
	    	WHERE `dataset`='" . addslashes($alias) . "'
	    	ORDER BY `ord` ASC 
	    	LIMIT 100");

    }

    public function getFields($alias)
    {

        return $this->query("SELECT `fields`.`alias`, `fields`.`field`, `fields`.`multiValue` 
	    	FROM `api_datasets_fields` AS `fields` 
	    	WHERE `fields`.`base_alias`='" . addslashes($alias) . "'
	    	LIMIT 100");

    }

    public function getFilterParams($alias, $field, $counts)
    {

        if (empty($counts))
            return false;

        $field = str_replace('_multidata_', '', $field);
        $filter = $this->query("SELECT `typ_id`, `params` FROM `datasets_filters` WHERE `dataset`='" . addslashes($alias) . "' AND `field`='" . addslashes($field) . "' LIMIT 1");
        if (!$filter)
            return false;

        $filter = $filter[0]['datasets_filters'];
        if (!$filter['typ_id'])
            return false;

        $params = @json_decode($filter['params'], 1);
        if (!$params)
            return false;


        if ($filter['typ_id'] == '1') {

            $ids = array();
            foreach ($counts as $id => $count)
                if ($id && $count)
                    $ids[] = $id;


            if (isset($params['table'])) {

                $id_field = isset($params['id_field']) ? $params['id_field'] : 'id';
                $title_field = isset($params['title_field']) ? $params['title_field'] : 'nazwa';
                $table = $params['table'];

                $data = $this->query("SELECT `$id_field` as 'id', `$title_field` as 'label' FROM `$table` WHERE `$id_field`='" . implode("' OR `$id_field`='", $ids) . "'");

                $dictionary = @array_column(array_column($data, $table), 'label', 'id');

                $options = array();
                foreach ($counts as $id => $count)
                    if ($id && $id != '_empty_' && $count)
                        $options[] = array(
                            'id' => $id,
                            'label' => array_key_exists($id, $dictionary) ? $dictionary[$id] : ' - ',
                            'count' => $count,
                        );

                $params['options'] = $options;

            }

        } elseif ($filter['typ_id'] == '2') {

            for ($i = 0; $i < count($params['options']); $i++)
                $params['options'][$i]['count'] = @$counts[$params['options'][$i]['id']];

        } elseif ($filter['typ_id'] == '5') {

            $ids = array();
            foreach ($counts as $id => $count)
                if ($id)
                    $ids[] = $id;


            $options = array();
            foreach ($counts as $id => $count)
                if ($id && $count)
                    $options[] = array(
                        'id' => $id,
                        'label' => $id,
                        'count' => $count,
                    );

            $params['options'] = $options;

        } else return false;

        return $params;
    }

}