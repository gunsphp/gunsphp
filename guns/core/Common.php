<?php

if (!function_exists('loadClass')) {

    function loadClass($className, $directory = 'core')
    {
        static $_classes = array();

        if (isset($_classes[$className])) {
            return $_classes[$className];
        }
        $classLoaded = App::uses($className, $directory);
        if ($classLoaded == false) {
            die('<strong>Class Not Found: ' . $className . ' within directory ' . $directory . '</strong>');
        }

        $_classes[$className] = new $className();
        loadedClasses($className);
        return $_classes[$className];
    }
}

if (!function_exists('loadedClasses')) {

    function loadedClasses($className = null)
    {
        static $_loadedClasses = array();
        if (!$className == null) {
            $_loadedClasses[$className] = $className;
        }
        return $_loadedClasses;
    }
}

if (!function_exists('e')) {

    function e($str, $wrapCode = false)
    {
        if (is_string($str) || is_numeric($str) || is_bool($str)) {
            if ($wrapCode) {
                echo "<code>" . $str . "</code>";
            } else {
                echo $str;
            }
        } elseif (is_array($str) || is_object($str)) {
            echo $wrapCode === true ? "<code><pre>" : "<pre>";
            print_r($str);
            echo $wrapCode === true ? "</pre></code>" : "</pre>";
        } else {
            echo $wrapCode === true ? "<code><pre>" : "<pre>";
            var_dump($str);
            echo $wrapCode === true ? "</pre></code>" : "</pre>";
        }
    }
}

if (!function_exists('isAjax')) {

    function isAjax($isGunsPHP = false)
    {
        switch ($isGunsPHP) {
            case true:
                return (bool)(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') === 0 && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' && !empty($_SERVER['HTTP_X_PHERY']));
            case false:
                return (bool)(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') === 0);
        }
        return false;
    }
}

if (!function_exists('exceptionHandler')) {

    function exceptionHandler($severity, $message, $file, $line)
    {
        if ($severity == E_STRICT) {
            return;
        }
        $_errors = loadClass('Exceptions');
        if (($severity & error_reporting()) == $severity) {
            echo $_errors->showErrors($severity, $message, $file, $line);
        }
        if (Configure::get('log.enabled')) {
            if (Configure::get('log.threshold') == 0) {
                return;
            }
            //$_errors->writeLog($severity, $message, $file, $line);
        }
    }
}

if (!function_exists('randomString')) {
    function randomString($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }
}

if (!function_exists('getMethods')) {
    function getMethods($className)
    {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $availableMethods = array();
        foreach ($methods as $key => $method) {
            if ($method->class == $className) {
                $availableMethods[$method->name] = $method->name;
            }
        }
        return $availableMethods;
    }
}

if (!function_exists('return_result')) {
    function return_result($userFunctionArray, $params = array())
    {
        ob_start();
        call_user_func_array($userFunctionArray, $params);
        $result = ob_get_contents();
        ob_end_clean();
        //ob_flush();
        /*if (ob_get_length() > 0) {
            ob_end_flush();
        }*/
        return $result;
    }
}

if (!function_exists('merge_array')) {
    function merge_array($array1, $array2)
    {
        $mergedArray = array_merge($array1, $array2);
        $mergedArray = array_map("unserialize", array_unique(array_map("serialize", $mergedArray)));
        return $mergedArray;
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $useInJs = false)
    {
        $u = loadClass('Url', 'helpers');
        $url = rtrim($url, '/');
        if (is_array($url)) {
            $url = $u->arrayToUrl($url);
        }
        if (!Text::startsWith($url, 'http://') || !Text::startsWith($url, 'https://')) {
            $url = $u->baseUrl() . $url;
        }
        if ($useInJs) {
            parseScript('window.location.href = "' . $url . '";' . "\n");
        } else {
            header("Location: $url");
        }
    }
}

if (!function_exists('baseUrl')) {

    function baseUrl()
    {
        $u = loadClass('Url', 'helpers');
        return $u->baseUrl();
    }
}