<?php
/**
* Zwraca obiekt GeoJson Feature zawierający obszar gminy (cache w redis) wraz z dynamicznie dociąganymi właściwościami
*/

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));
App::import('model', 'MPCache');

// Try cache
$cacheKey = 'geojson/gmina/' . $id  . ($simplify ? 's' : '');

$cache = new MPCache();
$cacheClient = $cache->getDataSource()->getRedisClient();
if ($cacheClient->exists($cacheKey)) {
    $geojson = json_decode($cache->get($cacheKey));

} else {
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

    if ($simplify) {
        // w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS (ale roznica wymiarow tylko 2x)
        $geom = $geom->simplify(0.006, true);
    }

    $geojsonConverter = new GeoJSON();
    $geojson = $geojsonConverter->write($geom, true);
    MpUtils::transposeCoordinates($geojson);

// Put in cache
    $cacheClient->set($cacheKey, json_encode($geojson));
}

$data = &$this->data['data'];

$feat = array(
    "type" => "Feature",
    "id" => $this->data['_id'],
    "properties" => array(
        'gminy.nazwa' => $data['gminy.nazwa'],
        'gminy.teryt' => $data['gminy.teryt'],
        'gminy.typ_nazwa' => $data['gminy.typ_nazwa'],
        'powiaty.id' => $data['powiaty.id'],
        'powiaty.nazwa' => $data['powiaty.nazwa'],
        'wojewodztwa.id' => $data['wojewodztwa.id'],
        'wojewodztwa.nazwa' => $data['wojewodztwa.nazwa'],
    ),
    "geometry" => $geojson
);
MpUtils::geoStampCRS($feat);

return $feat;
