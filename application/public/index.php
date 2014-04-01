<?php

date_default_timezone_set('Asia/Kolkata');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__FILE__))));
define('APP_ROOT', ROOT . DS . 'application');

define('CONFIG_DIR', APP_ROOT . DS . 'config');
define('APP_DIR', APP_ROOT . DS . 'app');
define('COMMON_DIR', APP_ROOT . DS . 'common');
define('GUNSPHP_DIR', ROOT . DS . 'guns');
define('PUBLIC_DIR', APP_ROOT . DS . 'public');
define('JS_DIR', PUBLIC_DIR . DS . 'js');
define('CSS_DIR', PUBLIC_DIR . DS . 'css');
define('IMG_DIR', PUBLIC_DIR . DS . 'img');
define('API_DIR', APP_ROOT . DS . 'api');
define('PLUGINS_DIR', APP_ROOT . DS . 'plugins');

define('APPCONTROLLER_DIR', COMMON_DIR . DS . 'controller');
define('ELEMENTS_DIR', COMMON_DIR . DS . 'elements');
define('LAYOUTS_DIR', COMMON_DIR . DS . 'layouts');

require GUNSPHP_DIR . DS . 'bootstrap.php';