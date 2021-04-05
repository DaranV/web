<?php

//on affiche toutes les erreurs
ini_set("display_errors", "1");
error_reporting(E_ALL);

//comme cloudflare est un proxy, l'adresse ip que apache récupère ne correspond pas à celle de l'utilisateur. On récitifie donc ça
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

defined("DS") || define("DS", DIRECTORY_SEPARATOR);
define("APPDIR", dirname(__DIR__) . "/stan/app/");
define("SYSTEMDIR", dirname(__DIR__) . "/stan/system/");
define("PUBLICDIR", dirname(__FILE__) . "/");
define("ROOTDIR", dirname(__DIR__) . "/stan/");
define("TMPDIR", ROOTDIR . "tmp/");
define("DIR", dirname(__DIR__) . "/");
define("ENVIRONMENT", "dev"); //dev ou prod
define("BASE_URL", "/");

require SYSTEMDIR . "Load/Autoload.php";
require SYSTEMDIR . "Load/Loader.php";
