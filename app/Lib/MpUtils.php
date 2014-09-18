<?
class MpUtils {

    public static function check_netmask($mask, $ip) {
        @list($net, $bits) = explode('/', $mask);
        $bits = isset($bits) ? $bits : 32;
        $bitmask = -pow(2, 32-$bits) & 0x00000000FFFFFFFF;
        $netmask = ip2long($net) & $bitmask;
        $ip_bits = ip2long($ip)  & $bitmask;

        return (($netmask ^ $ip_bits) == 0);
    }

    public static function is_trusted_client($ip) {
        if (!defined('TRUSTED_CLIENTS')) {
            return false;
        }

        // pack string in array
        $trusted = explode(',', TRUSTED_CLIENTS);

        foreach($trusted as $mask) {
            if (MpUtils::check_netmask(trim($mask), $ip)) {
                return true;
            }
        }
        return false;
    }

    public function setArray(&$array, $keys, $value) {
        //$keys = explode(".", $keys);
        $current = &$array;
        foreach($keys as $key) {
            $current = &$current[$key];
        }
        $current = $value;
    }

    public function pushInArray(&$array, $keys, $value) {
        //$keys = explode(".", $keys);
        $current = &$array;
        foreach($keys as $key) {
            $current = &$current[$key];
        }

        if (is_array($current)) {
            array_push($current, $value);
        } else {
            $current = array($value);
        }
    }

    public static function maptable2tree($table, $levels, $leaf_name) {
        $tree = array();
        $last = array();
        $last_level = array_pop($levels);

        $ptr = array_fill(0, 2*count($levels), 0);
        $ai = 0;
        foreach($levels as $lvl) {
            $ptr[$ai*2] = $lvl['name'];
            $ai++;
        }
        $ptr[] = $leaf_name;

        foreach($table as $row) {
            $ai = 0;
            foreach($levels as $lvl) {
                if ($lvl['key']($row) != @$last[$ai]) {
                    $ptr[$ai*2+1]++;
                    if (@$lvl['content'] != null) {
                        self::setArray($tree, array_slice($ptr, 0, ($ai+1)*2), $lvl['content']($row));
                    }

                    $zi = $ai*2+1+2;
                    while($zi < count($ptr)) {
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
}