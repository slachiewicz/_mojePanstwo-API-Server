<?php

namespace MpUtils;

class Url {
    private $parts = array();

    public function __construct($url = null) {
        if ($url != null) {
            $this->parts = $this->splitUrl($url);
        }
    }

    public function buildUrl() {
        return $this->joinUrl($this->parts);
    }

    public function set($part, $value) {
        $this->parts[$part] = $value;
    }

    public function setParam($param, $value) {
        if (!array_key_exists('query', $this->parts)) {
            $this->parts['query'] = array();
        }

        $this->parts['query'][$param] = $value;
    }

    public function removeParam($param) {
        if (array_key_exists('query', $this->parts)) {
            unset($this->parts['query'][$param]);
        }
    }


    /**
     * @see http://nadeausoftware.com/articles/2008/05/php_tip_how_parse_and_build_urls
     *
     * @param $url
     * @param bool $decode
     * @return mixed
     */
    public static function splitUrl($url, $decode = TRUE) {
        $xunressub = 'a-zA-Z\d\-._~\!$&\'()*+,;=';
        $xpchar = $xunressub . ':@%';

        $xscheme = '([a-zA-Z][a-zA-Z\d+-.]*)';

        $xuserinfo = '(([' . $xunressub . '%]*)' .
            '(:([' . $xunressub . ':%]*))?)';

        $xipv4 = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';

        $xipv6 = '(\[([a-fA-F\d.:]+)\])';

        $xhost_name = '([a-zA-Z\d-.%]+)';

        $xhost = '(' . $xhost_name . '|' . $xipv4 . '|' . $xipv6 . ')';
        $xport = '(\d*)';
        $xauthority = '((' . $xuserinfo . '@)?' . $xhost .
            '?(:' . $xport . ')?)';

        $xslash_seg = '(/[' . $xpchar . ']*)';
        $xpath_authabs = '((//' . $xauthority . ')((/[' . $xpchar . ']*)*))';
        $xpath_rel = '([' . $xpchar . ']+' . $xslash_seg . '*)';
        $xpath_abs = '(/(' . $xpath_rel . ')?)';
        $xapath = '(' . $xpath_authabs . '|' . $xpath_abs .
            '|' . $xpath_rel . ')';

        $xqueryfrag = '([' . $xpchar . '/?' . ']*)';

        $xurl = '^(' . $xscheme . ':)?' . $xapath . '?' .
            '(\?' . $xqueryfrag . ')?(#' . $xqueryfrag . ')?$';


        // Split the URL into components.
        if (!preg_match('!' . $xurl . '!', $url, $m))
            return FALSE;

        if (!empty($m[2])) $parts['scheme'] = strtolower($m[2]);

        if (!empty($m[7])) {
            if (isset($m[9])) $parts['user'] = $m[9];
            else            $parts['user'] = '';
        }
        if (!empty($m[10])) $parts['pass'] = $m[11];

        if (!empty($m[13])) $h = $parts['host'] = $m[13];
        else if (!empty($m[14])) $parts['host'] = $m[14];
        else if (!empty($m[16])) $parts['host'] = $m[16];
        else if (!empty($m[5])) $parts['host'] = '';
        if (!empty($m[17])) $parts['port'] = $m[18];

        if (!empty($m[19])) $parts['path'] = $m[19];
        else if (!empty($m[21])) $parts['path'] = $m[21];
        else if (!empty($m[25])) $parts['path'] = $m[25];

        if (!empty($m[27])) $parts['query'] = $m[28];
        if (!empty($m[29])) $parts['fragment'] = $m[30];

        if (!$decode)
            return $parts;
        if (!empty($parts['user']))
            $parts['user'] = rawurldecode($parts['user']);
        if (!empty($parts['pass']))
            $parts['pass'] = rawurldecode($parts['pass']);
        if (!empty($parts['path']))
            $parts['path'] = rawurldecode($parts['path']);
        if (isset($h))
            $parts['host'] = rawurldecode($parts['host']);
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $parts['query']);

            $q = array();
            foreach ($parts['query'] as $k => $v) {
                $q[rawurldecode($k)] = rawurldecode($v);
            }

            $parts['query'] = $q;
        }
        if (!empty($parts['fragment']))
            $parts['fragment'] = rawurldecode($parts['fragment']);
        return $parts;
    }

    public static function joinUrl($parts, $encode = TRUE) {
        if ($encode) {
            if (isset($parts['user']))
                $parts['user'] = rawurlencode($parts['user']);
            if (isset($parts['pass']))
                $parts['pass'] = rawurlencode($parts['pass']);
            if (isset($parts['host']) &&
                !preg_match('!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host'])
            )
                $parts['host'] = rawurlencode($parts['host']);
            if (!empty($parts['path']))
                $parts['path'] = preg_replace('!%2F!ui', '/',
                    rawurlencode($parts['path']));
            if (isset($parts['query'])) {
                $q = array();
                foreach ($parts['query'] as $k => $v) {
                    $q[rawurlencode($k)] = rawurlencode($v);
                }

                // if this function is undefined do `pecl install pecl_http-1.7.6` and add extension=http.so to php.ini
                $parts['query'] = http_build_query($q);
            }
            if (isset($parts['fragment']))
                $parts['fragment'] = rawurlencode($parts['fragment']);
        }

        $url = '';
        if (!empty($parts['scheme'])) {
            $url .= $parts['scheme'] . ':';
        }

        if (isset($parts['host'])) {
            if (empty($parts['scheme'])) {
                $url = 'http:';
            }
            $url .= '//';
            if (isset($parts['user'])) {
                $url .= $parts['user'];
                if (isset($parts['pass']))
                    $url .= ':' . $parts['pass'];
                $url .= '@';
            }
            if (preg_match('!^[\da-f]*:[\da-f.:]+$!ui', $parts['host']))
                $url .= '[' . $parts['host'] . ']'; // IPv6
            else
                $url .= $parts['host'];             // IPv4 or name
            if (isset($parts['port']))
                $url .= ':' . $parts['port'];
            if (!empty($parts['path']) && $parts['path'][0] != '/')
                $url .= '/';
        }
        if (!empty($parts['path'])) {
            $url .= $parts['path'];
        }

        if (isset($parts['query']) and $parts['query']) {
            $url .= '?' . $parts['query'];
        }

        if (isset($parts['fragment']))
            $url .= '#' . $parts['fragment'];
        return $url;
    }
}