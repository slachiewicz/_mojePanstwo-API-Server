<?

class solrSource extends DataSource
{

    public $API = false;
    public $description = 'Serwer SOLR platformy _mojePaństwo';

    private $_excluded_fields = array('datachannel', 'dataset', 'search', 'q');
    private $_fields_multi_dict = array();

    public function __construct($config)
    {

        require_once(APP . 'Vendor' . DS . 'Solr' . DS . 'Service.php');
        $this->API = new Apache_Solr_Service($config['host'], $config['port'], $config['core']);
        parent::__construct($config);

    }

    private function getFieldType($field)
    {

        if (strpos($field, 'data') === 0)
            return 'date';
        elseif (strpos($field, 'liczba') === 0)
            return 'int';
        elseif (in_array($field, array('rok', 'nr', 'numer', 'poz', 'pozycja', 'kolejnosc')))
            return 'int';

        return 'string';

    }

    public function read(Model $model, $queryData = array())
    {

        $__debug = false;
        $params = array();
        $mode = false;
        $userObject = ClassRegistry::init('Paszport.UserAdditionalData');
		$_dataset = false;
		$datasetOptions = array();
		

        // FIXING QUERY
        if (isset($queryData['conditions']['q'])) {

            $q = $queryData['conditions']['q'];
            unset($queryData['conditions']['q']);

            if (!isset($queryData['q']))
                $queryData['q'] = $q;

        }


        // PREPARING REQUEST VARIABLE

        $request = array(
            'q' => @solr_q($queryData['q']),

            'fields' => (isset($queryData['fields']) && is_array($queryData['fields'])) ?
                    $queryData['fields'] :
                    array('id', 'dataset', 'object_id', 'score', '_data_*', '_multidata_*'),

            'filters' => isset($queryData['conditions']) ?
                    $queryData['conditions'] :
                    array(),

            'raw_filters' => isset($queryData['raw_conditions']) ?
                    $queryData['raw_conditions'] :
                    array(),

            'order' => isset($queryData['order']) ?
                    $queryData['order'] :
                    array(),

            'limit' => (isset($queryData['limit']) && $queryData['limit']) ?
                    $queryData['limit'] :
                    10,

            'offset' => (isset($queryData['offset']) && $queryData['offset']) ?
                    $queryData['offset'] :
                    0,

            'facet' => (boolean)@$queryData['facets'],

            'switchers' => array(),

            'source' => false,
        );

        // var_export( $request ); die();

        // FIXING REQUEST

        $array_fields = array('fields', 'filters', 'raw_filters', 'order');
        foreach ($array_fields as $array_field) {

            if (!is_array($request[$array_field]))
                $request[$array_field] = array($request[$array_field]);

            $request[$array_field] = array_filter($request[$array_field]);

        }


        // PROCESSING REQUEST

        $user = ClassRegistry::init('Paszport.UserAdditionalData');
        $available_datasets = $userObject->getAvailableDatasets(Configure::read('Stream.id'));
        $dataset_switchers_exp_dict = array();
        $fields = array();
        $filters = array();
        $switchers = array();
        $orders = array();
        $fq_datasets = array();
        $order_parts = array();

        $fq_iterator = 0;
        $facet_iterator = 0;
        $querySearch = false;

        $params = array(
            'fl' => implode(', ', $request['fields']),
        );

        if ($request['q'] && !in_array($request['q'], array('*', '*:*'))) {

            $querySearch = true;
            $params = array_merge($params, array(
                'hl' => 'true',
                'hl.q' => str_replace('"', '', $request['q']),
                'hl.useFastVectorHighlighter' => 'true',
                'hl.fl' => 'hl_text',
                'hl.snippets' => 1,
                'hl.fragsize' => 200,
            ));

        }

        if ($request['facet'])
            $params['facet'] = 'on';

        if (@$request['filters']['dataset']) {
			
			if( !is_array($request['filters']['dataset']) )
				$request['filters']['dataset'] = array( $request['filters']['dataset'] );
			
			$datasets = array_intersect($request['filters']['dataset'], $available_datasets);

            if( empty($datasets) )
                return array(
                    'pagination' => array(
                        'total' => 0,
                    ),
                    'dataobjects' => array(),
                );
			
			
			if( count($datasets)===1 )
			{
				
				$_dataset = $datasets[0];
	            $datasetOptions = ClassRegistry::init('Dane.Dataset')->find('first', array(
	            	'conditions' => array(
	            		'Dataset.base_alias' => $_dataset,
	            	),
	            ));
	                     
	            $fields = ClassRegistry::init('Dane.Dataset')->getFields($_dataset, false);
	            $filters = ClassRegistry::init('Dane.Dataset')->getFilters($_dataset, false);
	            $switchers = ClassRegistry::init('Dane.Dataset')->getSwitchers($_dataset, true);
	            $orders = ClassRegistry::init('Dane.Dataset')->getSortings($_dataset, false);
            
            }

            $fq_datasets = array_merge($fq_datasets, $datasets);


        } elseif (@$request['filters']['datachannel']) {

            $datachannel = ClassRegistry::init('Dane.Datachannel')->find('first', array(
                'conditions' => array(
                    'Datachannel.slug' => $request['filters']['datachannel'],
                ),
            ));
            
			if( !empty($datachannel['Dataset']) )
	            foreach ($datachannel['Dataset'] as $dataset)
	                if (in_array($dataset['alias'], $available_datasets))
	                    $fq_datasets[] = $dataset['alias'];

            $filters[] = array(
                'filter' => array(
                    'field' => 'dataset',
                    'typ_id' => '1',
                    'multi' => '0',
                ),
            );

        } else {

            $datasets = ClassRegistry::init('Dane.Dataset')->find('list', array('fields' => array('id', 'alias')));

            foreach ($datasets as $dataset)
                if (in_array($dataset, $available_datasets))
                    $fq_datasets[] = $dataset;

            $filters[] = array(
                'filter' => array(
                    'field' => 'dataset',
                    'typ_id' => '1',
                    'multi' => '0',
                ),
            );

        }


        // PREPARING FIELDS DICTIONARY

        if (!empty($fields))
            foreach ($fields as $field)
                $this->_fields_multi_dict[$field['fields']['alias'] . '.' . $field['fields']['field']] = (boolean)@$field['fields']['multiValue'];


        // PREPARING SWITCHERS

        if (!empty($switchers)) {
            $dataset_switchers_exp_dict = array_column($switchers, 'switcher');
            $dataset_switchers_exp_dict = array_column($dataset_switchers_exp_dict, 'expression', 'name');
        }


        // PREPARING FACETS

        for ($i = 0; $i < count($filters); $i++) {


            $filter = $filters[$i]['filter'];
            $solr_field = $this->getSolrField($filter['field'], @$_dataset);
            $filters[$i]['filter']['solr_field'] = $solr_field;

            if ($request['facet'] && in_array($filter['typ_id'], array('1', '2', '5'))) {

                $prefix = '';

                if ($solr_field != 'dataset')
                    $prefix = '{!ex=' . $filter['field'] . '}';

                $params['facet.field[' . $facet_iterator . ']'] = $prefix . $solr_field;
                $facet_iterator++;

            }


        }

		if( !empty($fq_datasets) )
		{
	        $params['fq[' . $fq_iterator . ']'] = 'dataset:(' . implode(" OR ", $fq_datasets) . ')';
	        $fq_iterator++;
		}

        // PROCCESSING FILTERS

        if (!empty($request['filters'])) {

            if (isset($request['filters']['object_id']) && $request['filters']['object_id']) { # single object


                if (is_array($request['filters']['object_id'])) {
                    $params['fq[' . $fq_iterator . ']'] = 'object_id:(' . implode(" OR ", $request['filters']['object_id']) . ')';
                    $request['limit'] = count($request['filters']['object_id']);
                } else {
                    $params['fq[' . $fq_iterator . ']'] = 'object_id:' . $request['filters']['object_id'];
                    $request['limit'] = 1;
                }
                $fq_iterator++;


            } elseif (isset($request['filters']['id']) && $request['filters']['id']) { # single object


                if (is_array($request['filters']['id'])) {
                    $params['fq[' . $fq_iterator . ']'] = 'id:(' . implode(" OR ", $request['filters']['id']) . ')';
                    $request['limit'] = count($request['filters']['id']);
                } else {
                    $params['fq[' . $fq_iterator . ']'] = 'id:' . $request['filters']['id'];
                    $request['limit'] = 1;
                }
                $fq_iterator++;


            } elseif (isset($request['filters']['objects']) && $request['filters']['objects']) { # objects


                $parts = array();
                if (is_array($request['filters']['objects']))
                    foreach ($request['filters']['objects'] as $object)
                        $parts[] = '(dataset:' . $object['dataset'] . ' AND object_id:' . $object['object_id'] . ')';

                $request['limit'] = count($parts);
                $params['fq[' . $fq_iterator . ']'] = implode(" OR ", $parts);
                $fq_iterator++;


            } else { # data browsing

                foreach ($request['filters'] as $ckey => $cvalue) {

                    if (in_array($ckey, $this->_excluded_fields))
                        continue;


                    if ($ckey == '_source') {

                        $request['source'] = $cvalue;

                    } elseif ($ckey[0] == '!') {

                        $request['switchers'][] = substr($ckey, 1);

                    } else {


                        if ($cvalue != '') {


                            $solr_field = $this->getSolrField($ckey, @$_dataset);

                            if ($solr_field === false)
                                continue;


                            if (is_array($cvalue) && !empty($cvalue)) {

                                $vs = array();
                                foreach ($cvalue as $cv) {
                                    $cv = (string)$cv;
                                    if ($cv != '')
                                        $vs[] = $cv;
                                }

                                $cvalue = '';
                                if ($vs)
                                    $cvalue = implode(" OR ", $vs);
                            } else {
                                $cvalue = (string)$cvalue;
                            }

                            if ($cvalue != '')
                                $params['fq[' . $fq_iterator . ']'] = '{!tag=' . $ckey . '}' . $solr_field . ':(' . $cvalue . ')';

                            $fq_iterator++;

                        }


                    }

                }


                // SOURCES

                if ($request['source']) {

                    $source_params = array();
                    $source_parts = explode(' ', $request['source']);
                    foreach ($source_parts as $part) {

                        $p = strpos($part, ':');
                        if ($p !== false) {
                            $key = substr($part, 0, $p);
                            $value = substr($part, $p + 1);

                            if (($key != 'dataset') && ($key != 'datachannel'))
                                $source_params[$key] = $value;
                        }

                    }

                    if (!empty($source_params)) {
                        foreach ($source_params as $key => $value) {

                            switch ($key) {

                                case 'app':
                                {
                                	
                                	$_datasets = ClassRegistry::init('DB')->selectValues("SELECT base_alias FROM datasets WHERE app_id='" . addslashes($value) . "' AND `backup_catalog`='1'");
                                	
                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:(' . implode(' OR ', $_datasets) . ')';
                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'twitter.responsesTo':
                                {
                                    $params['fq[' . $fq_iterator . ']'] = '_data_in_reply_to_tweet_id:(' . $value . ')';
                                    $fq_iterator++;
                                    break;
                                }

                                case 'twitterAccounts.relatedTweets':
                                {
                                    $params['fq[' . $fq_iterator . ']'] = '_data_twitter_account_id:(' . $value . ') OR _data_in_reply_to_account_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }

                                case 'poslowie.aktywnosci':
                                {

                                    $mowca_id = ClassRegistry::init('DB')->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='" . addslashes($value) . "'");

                                    $fqs = array(
                                        '(dataset:sejm_wystapienia AND _data_ludzie.id:(' . $mowca_id . '))',
                                        '(dataset:legislacja_projekty_ustaw AND _multidata_posel_id:(' . $value . '))',
                                        '(dataset:legislacja_projekty_uchwal AND _multidata_posel_id:(' . $value . '))',
                                        '(dataset:sejm_interpelacje AND _multidata_posel_id:(' . $value . '))',
                                    );

                                    $params['fq[' . $fq_iterator . ']'] = implode(' OR ', $fqs);


                                    $fq_iterator++;
                                    break;
                                }

                                case 'poslowie.wystapienia':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_wystapienia AND _data_ludzie.posel_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }
                                
                                case 'poslowie.interpelacje':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_interpelacje AND _multidata_posel_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }
                                
                                case 'poslowie.projekty_ustaw':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:legislacja_projekty_ustaw AND _multidata_posel_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }
                                
                                case 'poslowie.glosowania':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie_glosy AND _data_posel_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }

                                case 'bdl_wskazniki_grupy.bdl_wskazniki':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:bdl_wskazniki AND _data_grupa_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }

                                case 'bdl_wskazniki_kategorie.bdl_wskazniki_grupy':
                                {

                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:bdl_wskazniki_grupy AND _data_kategoria_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }
                                
                                case 'rady_gmin_debaty.posiedzenie_id':
                                {
									
                                    $params['fq[' . $fq_iterator . ']'] = 'dataset:rady_gmin_debaty AND _data_posiedzenie_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;

                                }
                                
                                case 'crawlerSites.pages':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:crawler_pages AND _data_site_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'sejm_kluby.poslowie':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie AND _data_klub_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'sejm_komisje.poslowie':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie AND _multidata_komisja_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'gminy.zamowienia_publiczne':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:zamowienia_publiczne AND _data_gmina_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'gminy.organizacje':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:krs_podmioty AND _data_gmina_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'gminy.radni':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_gmin AND _data_gmina_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'gminy.dotacje_ue':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:dotacje_ue AND _multidata_gmina_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'sejm_posiedzenia.punkty':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_posiedzenia_punkty AND _data_posiedzenie_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'sejm_posiedzenia.wystapienia':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_wystapienia AND _data_posiedzenie_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'sejm_posiedzenia.glosowania':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_glosowania AND _data_posiedzenie_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'dzielnice.radni':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_dzielnic AND _data_dzielnica_id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }
                                
                                case 'gminy.radni_dzielnic':
                                {
	                                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_dzielnic AND _data_gminy.id:(' . $value . ')';

                                    $fq_iterator++;
                                    break;
                                }

                            }

                        }
                    }

                }


                // SWITCHERS

                if (!empty($request['switchers']) && !empty($dataset_switchers_exp_dict)) {
                    foreach ($request['switchers'] as $name) {
                        if ($exp = $dataset_switchers_exp_dict[$name]) {

                            $params['fq[' . $fq_iterator . ']'] = $exp;
                            $fq_iterator++;

                        }
                    }
                }


            }


        }


        // PROCCESSING RAW FILTERS

        foreach ($request['raw_filters'] as $raw_filter) {
            $params['fq[' . $fq_iterator . ']'] = $raw_filter;
            $fq_iterator++;
        }


        // PROCCESSING ORDERS

        $order = $request['order'];
        if (is_array($order) && !empty($order))
            $order = $order[0];

        if (is_array($order) && !empty($order))
            $order = $order[0];
			
		if ($request['q'] && !in_array($request['q'], array('*', '*:*')))
	        $solr_order_parts = array('score desc');


        if (!empty($order)) {

            $order = trim($order);
            $order_parts = explode(',', $order);

            foreach ($order_parts as $order_part) {

                if (!$order_part)
                    continue;

                $order_part_parts = explode(' ', $order_part);
                $order_part_parts_count = count($order_part_parts);

                if (!$order_part_parts_count)
                    continue;


                $solr_field = $this->getSolrField($order_part_parts[0], @$_dataset);


                if ($order_part_parts_count > 1)
                    $solr_field .= ' ' . $order_part_parts[1];
                else
                    $solr_field .= ' desc';

                $solr_order_parts[] = $solr_field;

            }

        }

        if (!empty($orders)) {
            $solr_field = $this->getSolrField($orders[0]['sorting']['field'], @$_dataset);
            $solr_order_parts[] = $solr_field . ' ' . $orders[0]['sorting']['direction'];
        }
		
		
		if( !empty($datasetOptions['Dataset']['default_sort']) )
		{
			$default_sort_parts = explode(',', $datasetOptions['Dataset']['default_sort']);
			if( !empty($default_sort_parts) )
				foreach( $default_sort_parts as $p )
					$solr_order_parts[] = trim( $p );			
		}
		
        $solr_order_parts[] = 'date desc';
                
        $params['sort'] = implode(', ', $solr_order_parts);


		/*
		echo "\n";
		echo $request['q'];
		echo "\n";
		var_export( $params );
		echo "\n";
		die();
        */


        $transport = $this->API->search($request['q'], $request['offset'], $request['limit'], $params);
        $raw_response = $transport->getRawResponse();

        $responseHeader = @get_object_vars($transport->responseHeader);
        $qParams = @get_object_vars($responseHeader['params']);

        $docs = $transport->response->docs;
        $hls = @get_object_vars($transport->highlighting);

        // Formatujemy wynik
        $resultSet = array(
            'pagination' => array(
                'total' => $transport->response->numFound,
            ),
        );

        $objects = array();
        foreach ($docs as $doc) {
            $id = $doc->getField('id');
            $dataset = $doc->getField('dataset');
            $object_id = $doc->getField('object_id');
            $score = $doc->getField('score');
            // $class = $_datasets_data[ $dataset['value'] ][0];
            if ($dataset && $object_id) {
                $data = array();
                foreach ($doc->getFieldNames() as $name) {
                    if (strpos($name, '_data_') === 0) {
                        $field_name = substr($name, 6);
                        $field_type = $this->getFieldType($field_name);

                        $field = $doc->getField($name);
                        $value = $field['value'];

                        if ($field_type == 'date') {

                            $ts = strtotime($value);
                            $value = date('Y-m-d', $ts);

                            $time = date('G:i:s', $ts);
                            if ($time != '0:00:00')
                                $value .= ' ' . $time;
                        }
                        $data[$field_name] = $value;

                    } elseif (strpos($name, '_multidata_') === 0) {
                        $field_name = substr($name, 11);
                        $field_type = $this->getFieldType($field_name);

                        $field = $doc->getField($name);
                        $value = $field['value'];

                        if (!is_array($value))
                            $value = array($value);
                        $data[$field_name] = $value;
                    }

                }

                // TEMP (SOLR ERROR):
                if (isset($data['data']) && stripos($data['data'], 'ERROR') === 0 && preg_match('/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/', $data['data'], $match))
                    $data['data'] = $match[0];

                $object = array(
                    'id' => $id['value'],
                    // 'class' => $class,
                    'dataset' => $dataset['value'],
                    'object_id' => $object_id['value'],
                    'data' => $data,
                    'score' => $score,
                );

                if (isset($hls[$object['id']]->hl_text)) {
                    $object['hl'] = array_pop($hls[$object['id']]->hl_text);
                }

                $objects[] = $object;
            }

        }

        $resultSet['dataobjects'] = $objects;


        if ($request['facet']) {

            // var_export( $transport->facet_counts->facet_fields ); die();
            // var_export( $filters ); die();

            $facets = array();
            foreach ($filters as $filter) {

                $filter = $filter['filter'];


                if (in_array($filter['typ_id'], array('1', '2', '5'))) {

                    $field = $filter['field'];
                    $solr_field = $filter['solr_field'];

                    $counts = @get_object_vars($transport->facet_counts->facet_fields->$solr_field);

                    if ($field == 'dataset') {

                        $ids = array();
                        foreach ($counts as $id => $count)
                            if ($id && $count)
                                $ids[] = $id;

                        $id_field = 'base_alias';
                        $title_field = 'name';
                        $table = 'datasets';

                        $data = ClassRegistry::init('DB')->query("SELECT `$id_field` as 'id', `$title_field` as 'label' FROM `$table` WHERE `$id_field`='" . implode("' OR `$id_field`='", $ids) . "'");

                        $dictionary = @array_column(array_column($data, $table), 'label', 'id');

                        $options = array();
                        foreach ($counts as $id => $count)
                            if ($id && $id != '_empty_' && $count)
                                $options[] = array(
                                    'id' => $id,
                                    'label' => array_key_exists($id, $dictionary) ? $dictionary[$id] : ' - ',
                                    'count' => $count,
                                );

                        $filter['params'] = array(
                            'options' => $options,
                        );

                    } elseif (@$_dataset) {

                        $filter['params'] = ClassRegistry::init('Dane.Dataset')->getFilterParams($_dataset, $field, $counts);

                    }

                    $facets[] = $filter;

                }
            }

            // echo "\n" . 'facets';
            // var_export( $facets ); die();
            $resultSet['facets'] = $facets;
        }
        return $resultSet;

    }

    private function getSolrField($field, $dataset = false)
    {
        /*
        echo "\n\ngetSolrField";
        echo "\n" . $field;
        echo "\n" . $dataset;
        */

        if (in_array($field, array('dataset', 'date', 'score')))
            return $field;

        $p = strpos($field, '.');


        $alternate_full_field;


        if ($p === false && $dataset) {
            $full_field = $dataset . '.' . $field;

            if ($dataset == 'ustawy')
                $alternate_full_field = 'prawo' . '.' . $field;
        } else {

            $full_field = $field;

            // TEMP - FIX IT!
            $field_dataset = substr($field, 0, $p);
            if ($dataset == 'ustawy') {
                if ($field_dataset == 'prawo') {
                    $field = substr($field, $p + 1);
                }
            }


        }

        $prefix = '_data_';

        if (array_key_exists($full_field, $this->_fields_multi_dict))
            $prefix = $this->_fields_multi_dict[$full_field] ? '_multidata_' : '_data_';
        elseif (isset($alternate_full_field) && array_key_exists($alternate_full_field, $this->_fields_multi_dict))
            $prefix = $this->_fields_multi_dict[$alternate_full_field] ? '_multidata_' : '_data_';
        else
            return false;

        return $prefix . $field;

    }

}