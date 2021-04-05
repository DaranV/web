<?php
use Core\Router;

Router::get('authentification', ["controller" => "AuthController", "method" => "auth", "name" => "auth", "middlewares" => ["NoAuthMiddleware"]]);
Router::get('inscription', ["controller" => "AuthController", "method" => "register", "name" => "register", "middlewares" => ["NoAuthMiddleware"]]);

Router::get('logout', ["controller" => "AuthController", "method" => "logout", "name" => "logout", "middlewares" => ["AuthMiddleware"]]);

Router::post('ajax/auth', ["controller" => "AuthController", "method" => "ajax_auth", "name" => "ajax_auth", "middlewares" => ["AjaxMiddleware"]]);
Router::post('ajax/register', ["controller" => "AuthController", "method" => "ajax_register", "name" => "ajax_register", "middlewares" => ["AjaxMiddleware"]]);


Router::get('account', ["controller" => "AuthController", "method" => "account", "name" => "account", "middlewares" => ["AuthMiddleware"]]);