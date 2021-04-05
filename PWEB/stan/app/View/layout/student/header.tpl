<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?= $title; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">

    <link href="http://fonts.googleapis.com/css?family=Lato:100,300,400,700" media="all" rel="stylesheet" type="text/css" />
    <link href="/assets/teacher/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/assets/teacher/css/font-awesome.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/assets/teacher/css/hightop-font.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/assets/teacher/css/social-buttons.css" media="all" rel="stylesheet" type="text/css" />
    
	<link rel="stylesheet" type="text/css" href="assets/auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/css/main.css">
	
    <?php if(Url::detectUri() == "/") : ?>
      <link href="assets/teacher/css/morris.css" media="all" rel="stylesheet" type="text/css" />
    <?php endif; ?>

    <link href="/assets/teacher/css/style.css" media="all" rel="stylesheet" type="text/css" />
	
	<?php if(@!is_null($css)): ?>
        <?= Assets::css($css); ?>
    <?php endif; ?>
	<style>
	body.page-header-fixed {
		padding-top: 80px;
	}
	</style>
  </head>
  <body class="page-header-fixed bg-1 layout-boxed">
    <div class="modal-shiftfix">
	<?php 
	View::setLayout("student");
	View::renderTemplate("navbar");
	?>