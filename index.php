<?php

date_default_timezone_set('Asia/Kolkata');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

define('CONFIG_DIR', ROOT . DS . 'config');
define('APP_DIR', ROOT . DS . 'app');
define('COMMON_DIR', ROOT . DS . 'common');
define('GUNSPHP_DIR', ROOT . DS . 'guns');
define('PUBLIC_DIR', ROOT . DS . 'public');
define('JS_DIR', PUBLIC_DIR . DS . 'js');
define('CSS_DIR', PUBLIC_DIR . DS . 'css');
define('IMG_DIR', PUBLIC_DIR . DS . 'img');
define('API_DIR', 'api');

define('APPCONTROLLER_DIR', COMMON_DIR . DS . 'controller');
define('ELEMENTS_DIR', COMMON_DIR . DS . 'elements');
define('LAYOUTS_DIR', COMMON_DIR . DS . 'layouts');

require GUNSPHP_DIR . DS . 'bootstrap.php';