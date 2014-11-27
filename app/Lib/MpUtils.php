<?

class MpUtils
{

    public static function check_netmask($mask, $ip)
    {
        @list($net, $bits) = explode('/', $mask);
        $bits = isset($bits) ? $bits : 32;
        $bitmask = -pow(2, 32 - $bits) & 0x00000000FFFFFFFF;
        $netmask = ip2long($net) & $bitmask;
        $ip_bits = ip2long($ip) & $bitmask;

        return (($netmask ^ $ip_bits) == 0);
    }

    public static function is_trusted_client($ip)
    {
        if (!defined('TRUSTED_CLIENTS')) {
            return false;
        }

        // pack string in array
        $trusted = explode(',', TRUSTED_CLIENTS);

        foreach ($trusted as $mask) {
            if (MpUtils::check_netmask(trim($mask), $ip)) {
                return true;
            }
        }
        return false;
    }

    public function setArray(&$array, $keys, $value)
    {
        //$keys = explode(".", $keys);
        $current = & $array;
        foreach ($keys as $key) {
            $current = & $current[$key];
        }
        $current = $value;
    }

    public function pushInArray(&$array, $keys, $value)
    {
        //$keys = explode(".", $keys);
        $current = & $array;
        foreach ($keys as $key) {
            $current = & $current[$key];
        }

        if (is_array($current)) {
            array_push($current, $value);
        } else {
            $current = array($value);
        }
    }

    public static function maptable2tree($table, $levels, $leaf_name)
    {
        $tree = array();
        $last = array();
        $last_level = array_pop($levels);

        $ptr = array_fill(0, 2 * count($levels), 0);
        $ai = 0;
        foreach ($levels as $lvl) {
            $ptr[$ai * 2] = $lvl['name'];
            $ai++;
        }
        $ptr[] = $leaf_name;

        foreach ($table as $row) {
            $ai = 0;
            foreach ($levels as $lvl) {
                if ($lvl['key']($row) != @$last[$ai]) {
                    $ptr[$ai * 2 + 1]++;
                    if (@$lvl['content'] != null) {
                        self::setArray($tree, array_slice($ptr, 0, ($ai + 1) * 2), $lvl['content']($row));
                    }

                    $zi = $ai * 2 + 1 + 2;
                    while ($zi < count($ptr)) {
                        $ptr[$zi] = 0;
                        $zi += 2;
                    }
                }

                $last[$ai] = $lvl['key']($row);
                $ai++;
            }

            self::pushInArray($tree, $ptr, $last_level['content']($row));
        }

        return $tree;
    }

    // ------ GEO functions -----------
    public static function transposeCoordinates(&$geojson)
    {
        $update_coordinates = function (&$geojson, $f) {
            if (!is_array($geojson['coordinates'][0][0][0])) {
                for ($i = 0; $i < count($geojson['coordinates']); $i++) {
                    for ($j = 0; $j < count($geojson['coordinates'][$i]); $j++) {
                        $geojson['coordinates'][$i][$j] = $f($geojson['coordinates'][$i][$j]);
                    }
                }
            } else {
                // MultiPolygon
                for ($i = 0; $i < count($geojson['coordinates']); $i++) {
                    for ($j = 0; $j < count($geojson['coordinates'][$i]); $j++) {
                        for ($k = 0; $k < count($geojson['coordinates'][$i][$j]); $k++) {
                            $geojson['coordinates'][$i][$j][$k] = $f($geojson['coordinates'][$i][$j][$k]);
                        }
                    }
                }
            }
        };

        // transpose coordinates from DB to be long,lat as required by GeoJSON
        $update_coordinates($geojson, function (&$coordinates) {
            return array(
                $coordinates[1],
                $coordinates[0]
            );
        });

        // transpose coordinates from WGS84 (EPSG:4326) to PL-2000 (EPSG:2177)
        $tmpfname = tempnam(null, "geo_") . '.json';
        $tmpfnameout = $tmpfname . '.out';
        $ogrstatus = 0;
        $tmpout = null;
        $type_pre = $geojson['type'];

        file_put_contents($tmpfname, json_encode($geojson));
        exec("ogr2ogr -f GeoJSON $tmpfnameout $tmpfname -s_srs EPSG:4326 -t_srs EPSG:2177", $tmpout, $ogrstatus);

        $geojson = json_decode(file_get_contents($tmpfnameout), true);
        exec("rm $tmpfname $tmpfnameout");

        // ogr2ogr modifies type to featurecollection
        $type_post = $geojson['type'];
        if ($type_pre != $type_post) {
            if (($type_pre == 'Polygon' || $type_pre == 'MultiPolygon') && $type_post == 'FeatureCollection') {
                $geojson = $geojson['features'][0]['geometry'];

            } else {
                throw new Exception("No implementation for $type_post -> $type_pre conversion");
            }
        }

        // cut the reminder, integer part is sufficiently detailed
        $update_coordinates($geojson, function (&$coordinates) {
            return array(
                intval($coordinates[0]),
                intval($coordinates[1])
            );
        });
    }

    public static function geoStampCRS(&$geojson, $crs = "urn:ogc:def:crs:EPSG:2177")
    {
        $geojson['crs'] = array(
            "type" => "name",
            "properties" => array(
                "name" => $crs
            )
        );
    }
}