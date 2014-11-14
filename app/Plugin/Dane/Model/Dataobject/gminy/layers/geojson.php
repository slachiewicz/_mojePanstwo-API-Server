<?php
App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/gmina/' . $id;

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    //TODO return json_decode($cache->get($cacheKey));
}

// Build geojson
$wkts = $this->DB->selectAssocs("SELECT mode, AsWKT(spat) AS wkt FROM pl_gminy_spats WHERE gmina_id = $id");

if ($wkts[0]['mode'] != 'ADD')
    throw new Exception("First geom should be the base with the mode = ADD");

$geom = geoPHP::load($wkts[0]['wkt'], 'wkt');

for ($i = 1; $i < count($wkts); $i++) {
    if ($wkts[$i]['mode'] == 'ADD') {
        $geom = $geom->union(geoPHP::load($wkts[$i]['wkt'], 'wkt'));

    } else if ($wkts[$i]['mode'] == 'DIFF') {
        $geom = $geom->difference(geoPHP::load($wkts[$i]['wkt'], 'wkt'));

    } else {
        throw new Exception("Unrecognized mode = " . $wkts[$i]['mode']);
    }
}

// TODO ? $simplified = $spat->simplify(1.0, true);
// w tej postaci bedzie to nierównomiernie robił w pionie i poziomie ze względu na CRS

$geojsonConverter = new GeoJSON();
$geojson = $geojsonConverter->write($geom, true);
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
