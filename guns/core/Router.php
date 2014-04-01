<?php

class Router
{

    static $_defaultAction;
    static $_routes;

    public static function initiate()
    {
        self::$_defaultAction = Configure::get('router.defaultAction');
        self::$_routes = array();
        $databaseRouterEnabled = Configure::get('router.databaseRouterEnabled');
        if ($databaseRouterEnabled) {
            loadClass('Cache');
            $dbRoutes = Cache::get('dbRoutes_in_Cache');
            if ($dbRoutes == false) {
                $dbRoutes = DbRoute::all();
                Cache::set('dbRoutes_in_Cache', $dbRoutes);
            }
            if ($dbRoutes !== false) {
                foreach ($dbRoutes as $r) {
                    $actualArray = array(
                        'controller' => $r->controller_name,
                        'action' => $r->action_name
                    );
                    Router::set($r->router_url, $actualArray, $r->router_name);
                }
            }
        }
    }

    public static function set($routerUrl, $actualUrl, $name = null)
    {
        if ($name == null) {
            $name = self::makeNameFromUrl($routerUrl);
        }
        $routerUrl = rtrim($routerUrl, '/');
        $routerUrl = ltrim($routerUrl, '/');
        $routerUrl = '/' . $routerUrl;
        if (is_array($actualUrl)) {
            self::$_routes[$name] = $actualUrl;
            self::$_routes[$routerUrl] = $actualUrl;
            return;
        }
        if (strpos($actualUrl, '/') !== false) {
            $route = array(
                'controller' => $actualUrl,
                'action' => self::$_defaultAction,
                array()
            );
        } else {
            $route = self::convertUrl($actualUrl);
        }
        self::$_routes[$name] = $route;
        self::$_routes[$routerUrl] = $route;
        self::$_routes['_router_map'][] = array($name, $routerUrl);
        return;
    }

    public static function get($actualUrlOrName)
    {
        if (Configure::get('router.enabled') && self::isRoute($actualUrlOrName)) {
            $returnRoute = self::checkRegex($actualUrlOrName);
            if (!$returnRoute) {
                $returnRoute = self::$_routes[$actualUrlOrName];
            }
            return $returnRoute;
        } else {
            return $actualUrlOrName;
        }
    }

    public static function isRoute($url)
    {
        $returnRoute = self::checkRegex($url);
        if (!$returnRoute) {
            if (isset(self::$_routes[$url])) {
                return true;
            }
            return false;
        } else {
            return true;
        }
    }

    public static function checkRegex($actualUrl)
    {
        $actualUrl = ltrim($actualUrl, '/');
        loadClass('Text', 'helpers');
        if (Text::endsWith($actualUrl, 'jpg') || Text::endsWith($actualUrl, 'png')) {
            return false;
        }
        $allRoutes = self::$_routes;
        if (count($allRoutes) > 0) {
            foreach ($allRoutes as $key => $value) {
                $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_\.\@]+)', preg_quote($key)) . "$@D";
                $matches = Array();
                preg_match($pattern, "/" . $actualUrl, $matches);
                if (count($matches) > 0) {
                    $returnValue = $value;
                    unset($matches[0]);
                    $returnValue[0] = $matches;
                    return $returnValue;
                }
            }
        }
        return false;
    }

    public static function convertUrl($url)
    {
        $urlClass = loadClass('Url', 'helpers');
        if ($urlClass->isFileRequested($url)) {
            return false;
        }
        if ($url == '') {
            $url = '/';
        }
        $urlExploded = explode('/', $url);
        $controller = ucwords(Configure::get('router.defaultController'));
        $action = ucwords(self::$_defaultAction);
        $isPlugin = false;
        $loadedPlugins = Plugin::getPlugins();
        if (isset($urlExploded[0]) && $urlExploded[0] !== '') {
            if (isset($loadedPlugins[ucwords($urlExploded[0])])) {
                if (isset($urlExploded[1]) && $urlExploded[1] !== '') {
                    $controller = ucwords($urlExploded[0]) . "." . ucwords($urlExploded[1]);
                    unset($urlExploded[1]);
                } else {
                    $controller = ucwords($urlExploded[0]) . "." . ucwords($controller);
                }
                $isPlugin = true;
                unset($urlExploded[0]);
            } else {
                $controller = ucwords($urlExploded[0]);
                unset($urlExploded[0]);
            }
        }
        if ($isPlugin) {
            if (isset($urlExploded[2]) && $urlExploded[2] !== '') {
                $action = ucwords($urlExploded[2]);
                unset($urlExploded[2]);
            } else {
                $action = $action;
            }
        } else {
            if (isset($urlExploded[1]) && $urlExploded[1] !== '') {
                $action = ucwords($urlExploded[1]);
                unset($urlExploded[1]);
            }
        }
        $params = self::removeBlankParams($urlExploded);
        $route = array(
            'controller' => $controller,
            'action' => $action,
            $params
        );
        return $route;
    }

    public static function makeNameFromUrl($url)
    {
        $url = str_replace("/", "_", $url);
        loadClass('Text', 'helpers');
        if (Text::contains('?', $url)) {
            $returnTemp = explode("?", $url);
            $url = $returnTemp[0];
        }
        return $url;
    }

    public static function removeBlankParams($params)
    {
        foreach ($params as $key => $param) {
            if ($param == '') {
                unset($params[$key]);
            }
        }
        return array_values($params);
    }

    public static function urlFromName($routeName, $params = array())
    {
        $route = self::$_routes[$routeName];
        if (!$route) {
            die('No Route with Name ' . $routeName . ' found.');
        }
        $allRoutes = self::$_routes;
        $proceed = false;
        foreach ($allRoutes as $key => $value) {
            if ($proceed == true) {
                $route = $key;
                break;
            }
            if ($key == $routeName) {
                $proceed = true;
            }
        }
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                $route = str_replace(":" . $key, $value, $route);
            }
        }
        $route = ltrim($route, '/');
        return $route;
    }
}