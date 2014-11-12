<?php
App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/powiat/' . $id;

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    return json_decode($cache->get($cacheKey));
}

// Build geojson
$wkt = $this->DB->selectAssoc("SELECT AsWKT(spat0) AS s0, AsWKT(spat1) AS s1, AsWKT(spat2) AS s2, AsWKT(hspat) AS hs FROM pl_powiaty WHERE id = $id");

if (!$wkt['s0'])
    return null;

$spat = geoPHP::load($wkt['s0'], 'wkt');

if ($wkt['s1']) {
    $s = geoPHP::load($wkt['s1'], 'wkt');
    $spat = $spat->union($s);
}

if ($wkt['s2']) {
    $s = geoPHP::load($wkt['s2'], 'wkt');
    $spat = $spat->union($s);
}

if ($wkt['hs']) {
    $s = geoPHP::load($wkt['hs'], 'wkt');
    $spat = $spat->difference($s);
}

// TODO ? $simplified = $spat->simplify(1.0, true);
// w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS

$geojsonConverter = new GeoJSON();
$geojson = $geojsonConverter->write($spat, true);
MpUtils::transposeCoordinates($geojson);

$p = &$this->data['data'];
$featCollection = array(
  "type" => "FeatureCollection",
  "features" => array(
       array(
           "type" => "Feature",
           "id" => $id, // TODO apiurl?
           "properties" => array(
                "nazwa" => $p['powiaty.nazwa'],
                "wojewodztwo_id" => $p['powiaty.wojewodztwo_id'],
                "wojewodztwo_nazwa" => $p['wojewodztwa.nazwa']
           ),
           "geometry" => $geojson
       )
  )
);

// Put in cache
$cacheClient->set($cacheKey, json_encode($featCollection), 'EX', 3600*24*7); // time in seconds

return $featCollection;
