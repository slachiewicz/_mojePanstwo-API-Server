<?php
/**
* Zwraca obiekt GeoJson FeatureCollection zawierający wszystkie gminy tego powiatu (właściwości są keszowane także)
*/

App::import('model', 'MPCache');
App::uses('Model', 'Dane.Dataobject');

// Try cache
$cacheKey = 'geojson/agg/powiaty/wojewodztwo/' . $id;

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    return json_decode($cacheClient->get($cacheKey));

} else {
    // Build geojson feature collection
    $powiaty_ids = $this->DB->selectValues("SELECT id FROM epf.pl_powiaty WHERE w_id = $id AND akcept = '1'");

    if (!$powiaty_ids) {
        throw new Exception("Nie znaleziono powiatów dla w_id = $id");
    }

    $powiaty = array();
    foreach($powiaty_ids as $pid) {
        $d = new Dataobject();
        $p = $d->getObjectLayer('powiaty', $pid, 'geojson', $params = array());

        unset($p['crs']);

        $powiaty[] = $p;
    }

    $featc = array(
        "type" => "FeatureCollection",
        "features" => $powiaty
    );
    MpUtils::geoStampCRS($featc);

    // Put in cache
    $cacheClient->set($cacheKey, json_encode($featc));

    return $featc;
}