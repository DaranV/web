<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;

$stan = Stan::getInstance();
?>

<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Bienvenue <?= Session::readUser("nom");?> <?= Session::readUser("prenom");?> !
        </h1>
    </div>
</div>