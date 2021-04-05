<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<div class="navbar navbar-fixed-top scroll-hide">
    <div class="container-fluid top-bar layout-boxed">
        <div class="pull-right">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown user hidden-xs"><a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <?= Session::readUser("prenom"); ?> <?= Session::readUser("nom"); ?><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/account">
                                <i class="fa fa-user"></i>Mon compte
                            </a>
                        </li>
                        <li>
                            <a href="/logout">
                                <i class="fa fa-sign-out"></i>Me déconnecter
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <button class="navbar-toggle">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="logo" href="/">PROJET PWEB</a>
    </div>
    <div class="container-fluid main-nav clearfix">
        <div class="nav-collapse">
            <ul class="nav">
                <li>
                    <a <?= (Url::detectUri() == "/") ? "class=\"current\"" : "" ?> href="/">
                        <span aria-hidden="true" class="fa fa-home"></span>Acceuil
                    </a>
                </li>
				<li>
                    <a <?= (Url::detectUri() == "/groups") ? "class=\"current\"" : "" ?> href="//<?= Url::connect("group_index");?>">
                        <span aria-hidden="true" class="fa fa-address-book"></span>Groupes
                    </a>
                </li>
				<li>
                    <a <?= (Url::detectUri() == "/students") ? "class=\"current\"" : "" ?> href="//<?= Url::connect("student_index");?>">
                        <span aria-hidden="true" class="fa fa-users"></span>Etudiants
                    </a>
                </li>
				<li>
                    <a <?= (Url::detectUri() == "/tests") ? "class=\"current\"" : "" ?> href="//<?= Url::connect("test_index");?>">
                        <span aria-hidden="true" class="fa fa-comments-o"></span>Tests
                    </a>
                </li>
				<li>
                    <a <?= (Url::detectUri() == "/themes") ? "class=\"current\"" : "" ?> href="//<?= Url::connect("theme_index");?>">
                        <span aria-hidden="true" class="fa fa-sticky-note-o"></span>Thèmes
                    </a>
                </li>
				<li>
                    <a <?= (Url::detectUri() == "/questions") ? "class=\"current\"" : "" ?> href="//<?= Url::connect("question_index");?>">
                        <span aria-hidden="true" class="fa fa-question"></span>Questions
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
