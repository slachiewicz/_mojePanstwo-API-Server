<?php

App::import('model', 'MPCache');
App::import('model', 'DB');

class GeoJsonController extends AppController
{
    public $uses = array('Dane.Dataobject', 'Geo.GeoJsonMP');

    private static $geoNames = array(
        'wojewodztwa',
        'gminy',
        'powiaty',
        'parl_okregi_sejm'
    );

	/*
    public function pl() {
        $this->viewClass = 'Media';

        $params = array(
            'id'        => 'pl.json',
            'name'      => 'pl',
            'download'  => false,
            'extension' => 'json',
            'cache' => true,
            'path'      => APP . 'Plugin/Geo/webroot/'
        );

        $this->set($params);
    }
    */

    private function getAggregatedGeojson($dataset, $table) {
        $this->DB = new DB();


		/*
        // Try cache
        $cacheKey = "geojson/agg/$dataset";

        $cache = new MPCache();
        $cacheClient = $cache->getDataSource()->getRedisClient();
        if ($cacheClient->exists($cacheKey)) {
            $featc = json_decode($cacheClient->get($cacheKey));

        } else {
            // Build geojson feature collection
            $ids = $this->DB->selectValues("SELECT id FROM $table WHERE akcept = '1'");

            if (!$ids) {
                throw new Exception("Nie znaleziono $dataset");
            }

            $features = array();
            foreach($ids as $id) {
                $d = new $this->Dataobject();
                $f = $d->getObjectLayer($dataset, $id, 'geojson_simplified', $params = array());

                unset($f['crs']);
                $features[] = $f;
            }

            $featc = array(
                "type" => "FeatureCollection",
                "features" => $features
            );

            MpUtils::geoStampCRS($featc);

            // Put in cache
            $cacheClient->set($cacheKey, json_encode($featc));
        }
        */


        $this->setSerialized('featc', $featc);
    }

    public function wojewodztwa() {
        $this->getAggregatedGeojson('wojewodztwa', 'wojewodztwa');
    }

    public function powiaty() {
        $this->getAggregatedGeojson('powiaty', 'pl_powiaty');
    }

    public function gminy() {
        $this->getAggregatedGeojson('gminy', 'pl_gminy');
    }

    public function get() {
        $params = (array) $this->request->query;
        $quality = $this->getQuality($params);
        $types = $this->getTypes($params);
        $elements = $this->getElements($params);
        $data = $this->GeoJsonMP->getMapData($quality, $types, $elements);
        $this->setSerialized('data', $data);
    }

    public function getLabel() {
        $params = (array) $this->request->query;
        $data = $this->GeoJsonMP->getLabel($params);
        $this->setSerialized('data', $data);
    }

    private function getElements($params) {
        $e = self::$geoNames;
        $elements = array();
        foreach($e as $type) {
            $elements[$type] = array();
            if(isset($params[$type])) {
                if(strpos('x'.$params[$type],',')) {
                    $ids = explode(',', $params[$type]);
                    foreach($ids as $id) {
                        $elements[$type][] = (int) $id;
                    }
                } else {
                    $elements[$type][] = (int) $params[$type];
                }
            }
        }

        return $elements;
    }

    private function getTypes($params) {
        $d = self::$geoNames;
        if(!isset($params['types']))
            return array($d[0]);
        if(strpos('x'.$params['types'],',')) {
            $types = explode(',', $params['types']);
            $t = array();
            foreach($types as $type) {
                if(in_array($type, $d))
                    $t[] = $type;
            }
            if(count($t) > 0) {
                return $t;
            } else {
                return array($d[0]);
            }
        } else {
            if(in_array($params['types'], $d))
                return array($params['types']);
            else
                return array($d[0]);
        }
    }

    private function getQuality($params) {
        $q = (int) @$params['quality'];
        if($q >= 1 && $q <= 4)
            return $q;
        return 4;
    }

    public function pl() {
	    $data = $this->GeoJsonMP->getData();
	    $this->setSerialized('data', $data);
    }
} 