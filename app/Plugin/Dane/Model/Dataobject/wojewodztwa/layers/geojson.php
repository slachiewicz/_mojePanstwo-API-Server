<?php
App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/wojewodztwo/' . $id;

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    // TODO return json_decode($cache->get($cacheKey));
}

// Build geojson
$wkt = $this->DB->selectAssoc("SELECT AsWKT(spat) AS wkt FROM wojewodztwa WHERE id = $id");

if (!$wkt['wkt'])
    return null;

$spat = geoPHP::load($wkt['wkt'], 'wkt');

// TODO ? $simplified = $spat->simplify(1.0, true);
// w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS
// może przetwarzać na http://spatialreference.org/ref/epsg/2175/?

$geojsonConverter = new GeoJSON();
$geojson = $geojsonConverter->write($spat, true);
MpUtils::transposeCoordinates($geojson);

$feat = array(
    "type" => "Feature",
    "id" => $id, // TODO apiurl?
    "properties" => $this->data['data'],
    "geometry" => $geojson
);

// Put in cache
$cacheClient->set($cacheKey, json_encode($feat), 'EX', 3600 * 24 * 7); // time in seconds

return $feat;
