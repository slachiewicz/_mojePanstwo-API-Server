<?php

/**
 * Zwraca obiekt GeoJson Feature zawierający obszar powiatu (cache w redis) wraz z dynamicznie dociąganymi właściwościami
 */

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/powiat/' . $id . ($simplify ? 's' : '');

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    $geojson = json_decode($cache->get($cacheKey));

} else {
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

    if ($simplify) {
        // w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS (ale roznica wymiarow tylko 2x)
        $spat = $spat->simplify(0.02, true);
    }

    $geojsonConverter = new GeoJSON();
    $geojson = $geojsonConverter->write($spat, true);
    MpUtils::transposeCoordinates($geojson);

// Put in cache
    $cacheClient->set($cacheKey, json_encode($geojson));
}

$data = &$this->data['data'];

$feat = array(
    "type" => "Feature",
    "id" => $this->data['_id'],
    "properties" => array(
        'powiaty.nazwa' => $data['powiaty.nazwa'],
        'wojewodztwa.id' => $data['wojewodztwa.id'],
        'wojewodztwa.nazwa' => $data['wojewodztwa.nazwa'],
    ),
    "geometry" => $geojson
);
MpUtils::geoStampCRS($feat);

return $feat;
