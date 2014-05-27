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
}