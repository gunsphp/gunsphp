<?php

require GUNSPHP_DIR . DS . 'core' . DS . 'App.php';

App::uses('Configure');
App::import('config', 'config');
App::import('autoload', 'config');

switch (Configure::get('debug.level')) {
    case 'development':
        error_reporting(E_ALL);
        break;
    case 'production':
        error_reporting(0);
        break;
    default:
        die('The Application\'s debug level is not configured correctly!');
}

$commonFile = App::uses('Common');
if ($commonFile == false) {
    die('Unable to locate GUNSPHP Framework File: Common.php');
}
set_error_handler('exceptionHandler');

App::uses('ActiveRecord', 'vendors' . DS . 'php-activerecord');
App::import('database', 'config');

if (Configure::get('database.appUsesDatabase')) {
    $connections = DB::getDbConfig();
    ActiveRecord\Config::initialize(function ($cfg) use ($connections) {
        $cfg->set_model_directory(COMMON_DIR . DS . 'Models');
        $cfg->set_connections($connections[Configure::get('database.defaultDriver')]);
    });

    App::uses('Model');

}

loadClass('Configure');
$loader = loadClass('Loader');

$urlClass = loadClass('Url', 'helpers');
$url = $urlClass->getUri();

App::import('Routes', 'config');

if (Router::isRoute($url)) {
    $routerControl = Router::get($url);
} else {
    $routerControl = Router::convertUrl($url);
}

if (strtolower($routerControl['controller']) == 'api') {
    $rest = loadClass('Rest');
    $r = explode("/", $url);
    unset($r[0]);
    $routerControl['version'] = isset($r[1]) ? $r[1] : $rest->response(json_encode(array('status' => 'FAILED', 'msg' => 'Missing Version')), 400);
    unset($r[1]);
    $routerControl['controller'] = isset($r[2]) ? $r[2] : $rest->response(json_encode(array('status' => 'FAILED', 'msg' => 'Missing Head Controller')), 400);
    unset($r[2]);
    $routerControl['action'] = isset($r[3]) ? $r[3] : $rest->response(json_encode(array('status' => 'FAILED', 'msg' => 'Missing Action')), 400);
    unset($r[3]);
    $routerControl['params'] = array_values($r);
    $routerControl['get'] = $_GET;

    $apiFolder = API_DIR . DS . $routerControl['version'] . DS . $routerControl['controller'];
    if (!is_dir($apiFolder) || !is_file($apiFolder . DS . $routerControl['action'] . '.php')) {
        $rest->response(json_encode(array(
            'status' => "FAILED",
            'msg' => 'INVALID API URL'
        )), 400);
    }
    $restAction = loadClass(ucwords($routerControl['action']), $apiFolder);

    $helpers = Configure::get('autoload.helpers');
    $libraries = Configure::get('autoload.libraries');

    if (isset($restAction->helpers)) {
        $helpers = array_merge($helpers, $restAction->helpers);
    }

    if (isset($restAction->libraries)) {
        $helpers = array_merge($libraries, $restAction->libraries);
    }

    foreach ($helpers as $helper) {
        $restAction->$helper = loadClass($helper, 'helpers');
    }

    foreach ($libraries as $library) {
        $restAction->$library = loadClass($library, 'libraries');
    }

    call_user_func_array(array($restAction, 'main'), $routerControl['params']);

} else {
    App::uses('JQuery');
    loadClass('Events');
    $loader->loadController($routerControl['controller'], $routerControl['action'], $routerControl[0]);
}
