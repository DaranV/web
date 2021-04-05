<?php
use Web\Url;
use Usage\Text;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Mon Compte
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur mon compte
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_my_account_prof");?>" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Nom</label>
                            <input class="form-control" id="nom" name="nom" type="text" value="<?= Session::readUser("nom"); ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Prénom</label>
                            <input class="form-control" id="prenom" name="prenom" type="text" value="<?= Session::readUser("prenom"); ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Email</label>
                            <input class="form-control" id="email" name="email" type="email" value="<?= Session::readUser("email"); ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Nom d'utilisateur</label>
                            <input class="form-control" id="username" name="username" type="text" value="<?= Session::readUser("login_prof"); ?>" required />
                        </div>
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
				</div>
            </div>
        </div>
		<div class="col-md-4" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Modifier le mot de passe
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_my_password_prof");?>" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Nouveau mot de passe</label>
                            <input class="form-control" id="password" name="password" type="password" required />
                        </div>
						<div class="form-group">
                            <label for="name">Confirmation</label>
                            <input class="form-control" id="rpassword" name="rpassword" type="password" required />
                        </div>
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
				 </div>
            </div>
        </div>
	</div>
</div>