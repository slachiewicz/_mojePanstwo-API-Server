<?php
/**
* Zwraca obiekt GeoJson FeatureCollection zawierający wszystkie gminy tego województwa (właściwości są keszowane także)
*/

App::import('model', 'MPCache');
App::uses('Model', 'Dane.Dataobject');

// Try cache
$cacheKey = 'geojson/agg/gminy/wojewodztwo/' . $id ;

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    return json_decode($cacheClient->get($cacheKey));

} else {
    // Build geojson feature collection
    $gminy_ids = $this->DB->selectValues("SELECT id FROM epf.pl_gminy WHERE w_id = $id AND akcept = '1'");

    if (!$gminy_ids) {
        throw new Exception("Nie znaleziono gmin dla w_id = $id");
    }

    $gminy = array();
    foreach($gminy_ids as $gid) {
        $d = new Dataobject();
        $g = $d->getObjectLayer('gminy', $gid, 'geojson_simplified', $params = array());

        unset($g['crs']);

        $gminy[] = $g;
    }

    $featc = array(
        "type" => "FeatureCollection",
        "features" => $gminy
    );

    // Put in cache
    $cacheClient->set($cacheKey, json_encode($featc));

    return $featc;
}