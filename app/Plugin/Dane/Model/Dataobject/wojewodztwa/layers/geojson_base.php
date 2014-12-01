<?php
/**
* Zwraca obiekt GeoJson Feature zawierający obszar województwa (cache w redis) wraz z dynamicznie dociąganymi właściwościami
*/

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/wojewodztwo/' . $id . ($simplify ? 's' : '');

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    $geojson = json_decode($cache->get($cacheKey));

} else {
    // Build geojson
    $wkt = $this->DB->selectAssoc("SELECT AsWKT(spat) AS wkt FROM wojewodztwa WHERE id = $id");

    if (!$wkt['wkt'])
        return null;

    $spat = geoPHP::load($wkt['wkt'], 'wkt');

    if ($simplify) {
        // w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS (ale roznica wymiarow tylko 2x)
        $spat = $spat->simplify(0.005, true);
    }

    $geojsonConverter = new GeoJSON();
    $geojson = $geojsonConverter->write($spat, true);
    MpUtils::transposeCoordinates($geojson);

    // Put in cache
    $cacheClient->set($cacheKey, json_encode($geojson));
}

$feat = array(
    "type" => "Feature",
    "id" => $this->data['_id'],
    "properties" => $this->data['data'],
    "geometry" => $geojson
);

return $feat;
