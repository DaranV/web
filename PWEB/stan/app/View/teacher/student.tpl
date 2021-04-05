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
            Compte : <?= $student->nom; ?> <?= $student->prenom; ?>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur l'étudiant
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_student");?>" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Nom</label>
                            <input class="form-control" id="nom" name="nom" type="text" value="<?= $student->nom; ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Prénom</label>
                            <input class="form-control" id="prenom" name="prenom" type="text" value="<?= $student->prenom; ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Email</label>
                            <input class="form-control" id="email" name="email" type="email" value="<?= $student->email; ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Nom d'utilisateur</label>
                            <input class="form-control" id="username" name="username" type="text" value="<?= $student->login_etu; ?>" required />
                        </div>
						<div class="form-group">
                            <label for="name">Matricule</label>
                            <input class="form-control" id="matricule" name="matricule" type="text" value="<?= $student->matricule; ?>" required />
                        </div>
						<input type="hidden" name="id" value="<?= $student->id_etu; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
					<button onclick="delStudent('//<?=Url::connect("ajax_delete_student", $student->id_etu);?>')" class="btn btn-danger">Supprimer le compte</button>
                </div>
            </div>
        </div>
		<div class="col-md-4" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Modifier le mot de passe
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_password_student");?>" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Nouveau mot de passe</label>
                            <input class="form-control" id="password" name="password" type="password" required />
                        </div>
						<div class="form-group">
                            <label for="name">Confirmation</label>
                            <input class="form-control" id="rpassword" name="rpassword" type="password" required />
                        </div>
						<input type="hidden" name="id" value="<?= $student->id_etu; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
				 </div>
            </div>
        </div>
		</div>
		<div class="row">
		<div class="col-md-12" id="qcm">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Tests traités par cet étudiant
                </div>
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="qcm-table">
                        <thead>
                            <th>#</th>
                            <th>Nom du test</th>
                            <th>Professeur référant</th>
                            <th>Résultat</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
							<?php foreach($bilans as $test) : ?>
                                <tr>
                                    <td><span id="bilan_<?= $test->id_test; ?>"><?= $i ?></span></td>
                                    <td><?= $test->titre_test; ?></td>
                                    <td><?= $test->teacher->nom; ?> <?= $test->teacher->prenom; ?></td>
                                    <td><?= ($test->bilan == null) ? "Résultat Inconnu" : $test->bilan->note_test . "/" . $test->total; ?></td>
                                    <td><?= ($test->bilan == null) ? "Date Inconnue" : $test->bilan->date_bilan; ?></td>
                                    <td><a href="//<?= Url::connect("student_view_bilan", $student->id_etu, $test->id_test);?>" class="btn btn-info">Détail du test</a></td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>