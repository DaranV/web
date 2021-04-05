<?php
require ROOTDIR . "vendor/autoload.php";

use Session\Session;
use Stan\Stan;
use Core\Router;

if (file_exists(ROOTDIR . 'vendor/autoload.php')) {
    require ROOTDIR . 'vendor/autoload.php';
}
else {
    trigger_error("autoload.php does not exist", E_USER_ERROR);
}

if (!is_readable(ROOTDIR . '/config/config.ini') && $installed == true) {
    trigger_error("config.ini does not exist", E_USER_ERROR);
}

if (!is_readable(ROOTDIR . '/config/database.ini') && $installed == true) {
    trigger_error("database.ini does not exist", E_USER_ERROR);
}

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'dev':
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
            break;
        case 'prod':
            ini_set('display_errors', '0');
            error_reporting(0);
            break;
        default:
            trigger_error("const ENVIRONMENT does not configure", E_USER_ERROR);
    }
}

Session::init();
$stan = Stan::getInstance();
$router = Router::getInstance();
date_default_timezone_set($stan->configs->config->get("TIMEZONE"));
setlocale (LC_TIME, 'fr_FR.utf8','fra');
ini_set('intl.default_locale', 'fr_FR.utf8');
Router::error('App\Controller\ErrorController@index');
$router->dispatch();
ob_start();
?>
