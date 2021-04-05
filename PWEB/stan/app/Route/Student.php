<?php
use Core\Router;

Router::get('my-bilan/(:num)', ["controller" => "StudentController", "method" => "view_bilan", "name" => "view_my_bilan", "middlewares" => ["AuthMiddleware"]]);

Router::post('ajax/sendAnswer', ["controller" => "StudentController", "method" => "ajax_sendAnswer", "name" => "ajax_sendAnswer", "middlewares" => ["AjaxMiddleware", "AuthMiddleware"]]);
Router::post('ajax/register-group', ["controller" => "StudentController", "method" => "ajax_set_group", "name" => "ajax_set_group", "middlewares" => ["AjaxMiddleware", "AuthMiddleware"]]);
Router::get('ajax/live', ["controller" => "StudentController", "method" => "ajax_live", "name" => "ajax_live", "middlewares" => ["AjaxMiddleware", "AuthMiddleware"]]);
Router::post('ajax/edit-account', ["controller" => "StudentController", "method" => "ajax_edit", "name" => "ajax_edit_my_account", "middlewares" => ["AuthMiddleware", "CorsMiddleware"]]);
Router::post('ajax/edit-password', ["controller" => "StudentController", "method" => "ajax_edit_password", "name" => "ajax_edit_my_password", "middlewares" => ["AuthMiddleware", "CorsMiddleware"]]);
