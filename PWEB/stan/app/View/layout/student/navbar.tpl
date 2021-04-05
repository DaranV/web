<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<style>
.navbar {
	height: auto;
}

.navbar .container-fluid.top-bar{
	border-bottom: none;
}
</style>
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
</div>
