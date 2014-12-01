<?php

App::import('model', 'MPCache');
App::import('model', 'DB');

class GeoJsonController extends AppController
{
    public $uses = array('Dane.Dataobject');

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

    private function getAggregatedGeojson($dataset, $table) {
        $this->DB = new DB();

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
                $f = $d->getObjectLayer($dataset, $id, ($dataset == 'wojewodztwa') ? 'geojson' : 'geojson_simplified', $params = array());

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
} 