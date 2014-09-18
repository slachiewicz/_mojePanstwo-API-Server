<?php

define('API_BDL_MISMATCHED_DIMS_COUNT', 'API_BDL_MISMATCHED_DIMS_COUNT');
define('API_BDL_INVALID_SLICE', 'API_BDL_INVALID_SLICE');
define('API_BDL_INVALID_INPUT', 'API_BDL_INVALID_INPUT');
define('API_BDL_TOO_MANY_POINTS', 'API_BDL_TOO_MANY_POINTS');
define('API_BDL_LEVEL_DATA_NOTAVAILABLE', 'API_BDL_LEVEL_DATA_NOTAVAILABLE');

define('DATA_POINTS_LIMIT', 500);

class BDLController extends AppController
{
    public $uses = array('Dane.Dataobject', 'BDL.Podgrupa', 'BDL.DataPl', 'BDL.DataWojewodztwa', 'BDL.DataGminy', 'BDL.DataPowiaty', 'BDL.WymiaryKombinacje');

    public function tree()
    {
        $tree_data = ConnectionManager::getDataSource('default')->query(
"SELECT pg.tytul AS pg_tytul, pg.id AS pg_id, g.tytul AS g_tytul, g.id AS g_id,
    k.w_tytul AS k_tytul, k.id AS k_id
FROM epf.BDL_podgrupy pg INNER JOIN BDL_grupy g ON (pg.grupa_id = g.id)
    INNER JOIN BDL_kategorie k ON (pg.kategoria_id = k.id)
WHERE k.okres = 'R' AND k.deleted = '0' AND g.deleted = '0' AND pg.deleted = '0'
ORDER BY k_id ASC, g_id ASC, pg_tytul ASC;");

        $tree = array();
        $kategoria = null;
        $grupa = null;

        $last_kategoria = null;
        $last_grupa = null;

        foreach($tree_data as $row) {
            if ($row['g']['g_id'] != $last_grupa) {
                if ($grupa != null)
                    array_push($kategoria['groups'], $grupa);

                $grupa = array(
                    'subgroups' => array(),
                    'name' => $row['g']['g_tytul']
                );
            }
            if ($row['k']['k_id'] != $last_kategoria) {
                if ($kategoria != null)
                    array_push($tree, $kategoria);

                $kategoria = array(
                    'groups' => array(),
                    'name' => $row['k']['k_tytul']
                );
            }

            array_push($grupa['subgroups'], array(
                'name' => $row['pg']['pg_tytul'],
                '_id' => Router::url(array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'view', 'alias' => 'bdl_wskazniki', 'object_id' => $row['pg']['pg_id']), true)
            ));

            $last_grupa = $row['g']['g_id'];
            $last_kategoria = $row['k']['k_id'];
        }

        // TODO redis cache

        $this->setSerialized('tree', $tree);
    }

    public function search() {
        $search = array();

        $q = @$this->request->query['q'];
        if( $q )
        {
            $data = $this->Dataobject->search(array('bdl_wskazniki', 'bdl_wskazniki_grupy', 'bdl_wskazniki_kategorie'), array(
                'conditions' => array(
                    'q' => $q,
                ),
//                'mode' => 'title_prefix',
                'limit' => 10,
            ));
            if( isset($data['dataobjects']) && !empty($data['dataobjects']) )
            {
                $search = $data['dataobjects'];
            }
        } else {
            throw new BadRequestException('Query parameter is required: q');
        }

        $this->setSerialized('search', $search);
    }

    public function series() {
        $metric_id = @$this->request->query['metric_id'];
        $slice = @$this->request->query['slice'];
        $time_range = @$this->request->query['time_range'];
        $wojewodztwo_id = @$this->request->query['wojewodztwo_id'];
        $powiat_id = @$this->request->query['powiat_id'];
        $gmina_id = @$this->request->query['gmina_id'];
        $meta = @$this->request->query['meta'];

        if ($metric_id === null) {
            throw new BadRequestException('Query parameter is required: metric_id');
        }

        $metric = $this->Dataobject->getObject('bdl_wskazniki', $metric_id, array(
            'layers' => 'dimennsions',
            'conditions' => array(
                'okres' => 'R',
                'deleted' => '0'
            )
        ));

        if (!$metric) {
            throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'metric_id'));
        }

        $metric_depths = array(
            '0' => 'pl',
            '2' => 'wojewodztwo',
            '4' => 'powiat',
            '5' => 'gmina',
        );

        $dims = $metric['layers']['dimennsions'];

        // check if slices match metrics
        if ($slice != null) {
            if (!preg_match('/^\\[(\\d+(,?\\d+)*)\\]$/', $slice, $slices)) {
                throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'slice'));
            }

            $slices = explode(',', $slices[1]);
            if (count($slices) != count($dims)) {
                throw new ApiException(API_BDL_MISMATCHED_DIMS_COUNT, array('expected' => count($dims), 'was' => count($slices)));
            }

        } else {
            $slices = array_fill(0, count($dims), '*');
        }

        $w = array(0=>0, 1=>0, 2=>0, 3=>0, 4=>0);
        for($i = 0; $i < count($slices); $i++) {
            $dim_slices = array_map(function($el) {
                return $el['id'];
            }, $dims[$i]['options']);

            if ($slices[$i] == '*') {
                $w[$i] = $dim_slices;

            } elseif(!in_array($slices[$i], $dim_slices)) {
                throw new ApiException(API_BDL_INVALID_SLICE, $slices[$i]);

            } else {
                $w[$i] = $slices[$i];
            }
        }

        if (($wojewodztwo_id != null) + ($powiat_id != null) + ($gmina_id != null) > 1) {
            throw new ApiException(API_BDL_INVALID_INPUT, array('param' => array('wojewodztwo_id', 'powiat_id', 'gmina_id')), 'Only one of these parameters can be specified');
        }

        $conditions = array();
        $fields = array('w1', 'w2', 'w3', 'w4', 'w5', 'rocznik', 'v', 'kombinacja_id');

        // choose datasource
        $region = null;

        if ($wojewodztwo_id != null) {
            $model = 'DataWojewodztwa';
            $fields[] = 'wojewodztwo_id';

            if ($metric['data']['poziom_id'] < 2) {
                throw new ApiException(API_BDL_LEVEL_DATA_NOTAVAILABLE, array(
                    'requested' => $metric_depths[2],
                    'available_till' => $metric_depths[$metric['data']['poziom_id']]
                ), 'Data at this regional level is not available');
            }

            if ($wojewodztwo_id != '*') {
                if (!preg_match('/^\d+$/', $wojewodztwo_id)) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'wojewodztwo_id'));
                }

                $wojewodztwo_id = intval($wojewodztwo_id);
                $region = $this->Dataobject->getObject('wojewodztwa', $wojewodztwo_id);
                if (!$region) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'wojewodztwo_id'));
                }

                $conditions["wojewodztwo_id"] = $wojewodztwo_id;
            }

        } else if ($powiat_id != null) {
            $model = 'DataPowiaty';
            $fields[] = 'powiat_id';

            if ($metric['data']['poziom_id'] < 4) {
                throw new ApiException(API_BDL_LEVEL_DATA_NOTAVAILABLE, array(
                    'requested' => $metric_depths[4],
                    'available_till' => $metric_depths[$metric['data']['poziom_id']]
                ), 'Data at this regional level is not available');
            }

            if ($powiat_id != '*') {
                if (!preg_match('/^\d+$/', $powiat_id)) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'powiat_id'));
                }

                $powiat_id = intval($powiat_id);
                $region = $this->Dataobject->getObject('powiaty', $powiat_id);
                if (!$region) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'powiat_id'));
                }

                $conditions["powiat_id"] = $powiat_id;
            }

        } else if ($gmina_id != null) {
            $model = 'DataGminy';
            $fields[] = 'gmina_id';

            if ($metric['data']['poziom_id'] < 5) {
                throw new ApiException(API_BDL_LEVEL_DATA_NOTAVAILABLE, array(
                    'requested' => $metric_depths[5],
                    'available_till' => $metric_depths[$metric['data']['poziom_id']]
                ), 'Data at this regional level is not available');
            }

            if ($gmina_id != '*') {
                if (!preg_match('/^\d+$/', $gmina_id)) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'gmina_id'));
                }

                $gmina_id = intval($gmina_id);
                $region = $this->Dataobject->getObject('gminy', $gmina_id);
                if (!$region) {
                    throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'gmina_id'));
                }

                $conditions["gmina_id"] = $gmina_id;
            }

        } else {
            $model = 'DataPl';
        }

        // choose time range
        if ($time_range != null) {
            if (!preg_match('/^(\\d{4}):(\\d{4})$/', $time_range, $time_ranges)) {
                throw new ApiException(API_BDL_INVALID_INPUT, array('param' => 'time_range', 'value' => $time_range, 'expected_format' => 'year_start:year_end'));
            }

            if ($time_ranges[0] <= $time_ranges[1]) {
                $conditions['rocznik >='] = intval($time_ranges[0]);
                $conditions['rocznik <='] = intval($time_ranges[1]);

            } else {
                $conditions['rocznik >='] = intval($time_ranges[1]);
                $conditions['rocznik <='] = intval($time_ranges[2]);
            }
            // TODO co z missing values?
        }

        // choose series
        $series_ids = $this->WymiaryKombinacje->find('all', array(
            'conditions' => array(
                'podgrupa_id' => $metric_id,
                'w1' => $w[0],
                'w2' => $w[1],
                'w3' => $w[2],
                'w4' => $w[3],
                'w5' => $w[4],
            ),
            'fields' => array('id', 'jednostka')
        ));
        $units = array();
        foreach($series_ids as $s) {
            $units[$s['WymiaryKombinacje']['id']] = trim($s['WymiaryKombinacje']['jednostka'], '[]');
        }
        $conditions['kombinacja_id'] = array_keys($units);

        $conditions['deleted'] = 0;

        $data_count = $this->$model->find('count', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => array('kombinacja_id', 'rocznik'),
        ));

        if ($data_count > DATA_POINTS_LIMIT) {
            throw new ApiException(API_BDL_TOO_MANY_POINTS, array('limit' => DATA_POINTS_LIMIT));
        }

        $data = $this->$model->find('all', array(
            'conditions' => $conditions,
            'fields' => $fields,
            'order' => array('kombinacja_id', 'rocznik'),
        ));

        $slice_parts = array_slice(array('w1'=>0, 'w2'=>0, 'w3'=>0, 'w4'=>0, 'w5'=>0), 0, count($slices));
        $response = MpUtils::maptable2tree($data, array(
                   array('name' => 'series', 'key' => function($r) use($model) {
                           return $r[$model]['kombinacja_id'] . '-' . @$r[$model]['wojewodztwo_id']; // TODO rest regions
                       }, 'content' => function($r) use($model, $slice_parts, $units) {
                           $legend = array(
                               'slice' => array_values(array_intersect_key($r[$model], $slice_parts)),
                               'units' => $units[$r[$model]['kombinacja_id']]);

                           if (@$r[$model]['wojewodztwo_id'])
                                $legend['wojewodztwo_id'] = Dataobject::apiUrlStd('wojewodztwa', $r[$model]['wojewodztwo_id']);

                           if (@$r[$model]['powiat_id'])
                               $legend['powiat_id'] = Dataobject::apiUrlStd('powiaty', $r[$model]['powiat_id']);

                           if (@$r[$model]['gmina_id'])
                               $legend['gmina_id'] = Dataobject::apiUrlStd('gminy', $r[$model]['gmina_id']);

                           return $legend;
                       }),
                   array('content' => function($r) use($model) {
                           return array('year' => intval($r[$model]['rocznik']), 'value' => $r[$model]['v']);
                       })
                ), 'series');

        if ($meta !== '0') {
            // include meta by default
            $response['meta'] = array(
                'metric_id' =>  Dataobject::apiUrl($metric),
                'metric_name' => $metric['data']['tytul'],
                'metric_depth' => $metric_depths[$metric['data']['poziom_id']],
                'metric_mpurl' => Dataobject::mpUrl($metric),
                'group_name' => $metric['data']['grupa_tytul'],
                'category_name' => $metric['data']['kategoria_tytul'],
                'dimensions' => $dims,
            );
        }

        if (empty($response)) {
            $response = new object();
        }

        $this->setSerialized('response', $response);
    }
}