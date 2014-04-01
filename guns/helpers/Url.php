<?php


class Url
{

    public function getUri()
    {
        if (!isset($_SERVER['REQUEST_URI']) or !isset($_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        $configureBaseUrl = Configure::get('baseurl');
        $uri = $_SERVER['HTTP_HOST'] . $uri;
        if (isset($_SERVER['HTTPS'])) {
            $uri = 'https://' . $uri;
        } else {
            $uri = 'http://' . $uri;
        }
        if (!$configureBaseUrl == '') {
            $uri = str_replace($configureBaseUrl, '', $uri);
        } else {
            $uri = str_replace($this->baseUrl(), '', $uri);
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if (strncmp($uri, '?/', 2) === 0) {
            $uri = substr($uri, 2);
        }
        $parts = preg_split('#\?#i', $uri, 2);
        $uri = $parts[0];
        if (isset($parts[1])) {
            $_SERVER['QUERY_STRING'] = $parts[1];
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        } else {
            $_SERVER['QUERY_STRING'] = '';
            $_GET = array();
        }

        if ($uri == '/' || empty($uri)) {
            return '/';
        }

        $uri = parse_url($uri, PHP_URL_PATH);
        // Do some final cleaning of the URI and return it
        return str_replace(array('//', '../'), '/', trim($uri, '/'));
    }

    public function baseUrl($atRoot = null, $atCore = false, $parse = false)
    {
        $atRoot = $atRoot == null ? Configure::get('baseurl.pointToRoot') : $atRoot;
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ?
                'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__file__))), null, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ?
                "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else
            $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path']))
                if ($base_url['path'] == '/')
                    $base_url['path'] = '';
        }

        return $base_url;
    }

    public function arrayToUrl($url)
    {
        if (!isset($url['controller']))
            return false;
        $controllerName = $url['controller'];
        $actionName = isset($url['action']) ? $url['action'] : "index";
        $url['action'] = $actionName;
        unset($url['controller']);
        unset($url['action']);
        $returnUrl = baseUrl() . "$controllerName/$actionName";
        if (count($url) > 0) {
            foreach ($url as $key => $value) {
                $returnUrl .= "/$value";
            }
        }
        return $returnUrl;
    }

    public function isFileRequested($url)
    {
        $extensionsAvailable = Configure::get('url.allowedExtensions');
        foreach ($extensionsAvailable as $extn) {
            if (Text::endsWith($url, $extn)) {
                return true;
            }
        }
        return false;
    }

}