<?php
use Core\Router;

Router::get('',["controller" => "HomeController",	"method" => "index",  "name" => "home", "middlewares" => ["AuthMiddleware"]]);