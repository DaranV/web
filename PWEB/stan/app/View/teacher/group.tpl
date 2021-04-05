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
            Groupe : <?= $group->num_grpe; ?>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="add">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le groupe
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_group");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Nom du groupe</label>
                            <input class="form-control" id="name" name="name" type="text" value="<?= $group->num_grpe; ?>"/>
                        </div>
						<div class="form-group">
                            <label for="name">Code du groupe</label>
                            <span class="form-control"><?= $group->code; ?></span>
                        </div>
						<input type="hidden" name="id" value="<?= $group->id_grpe; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8" id="classes">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Etudiant(s) de ce groupe
                </div>
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="students-table">
                        <thead>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Genre</th>
                            <th>Matricule</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
							<?php foreach($students as $student) : ?>
                                <tr>
                                    <td><span id="student_<?= $student->id_etu; ?>"><?= $i ?></span></td>
                                    <td><?= $student->nom; ?></td>
                                    <td><?= $student->prenom; ?></td>
                                    <td><?= $student->genre; ?></td>
                                    <td><?= $student->matricule; ?></td>
                                    <td>
                                        <button onclick="del('//<?= Url::connect("ajax_remove_student_group", $group->id_grpe, $student->id_etu);?>')" class="btn btn-danger">Supprimer</button>
										<a href="/student/<?= $student->id_etu; ?>/<?= Text::slug($student->nom . "-" . $student->prenom); ?>" class="btn btn-info">Voir</a>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		</div>
		<div class="row">
		 <div class="col-md-offset-4 col-md-8" id="add-student">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Ajouter un étudiant au groupe
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_add_student_group");?>" method="post">
						<div id="result"></div>
                        <div class="form-group" id="class_input">
							<label for="name">Classe</label>
							<select class="form-control" name="student_id" id="student_id">
								<?php foreach($studentsA as $student) : ?>
									<option value="<?= $student->id_etu; ?>"><?= $student->nom . ' ' . $student->prenom; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="hidden" name="id" value="<?= $group->id_grpe; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
		</div>
		<div class="row">
		<div class="col-md-8 col-md-offset-4" id="tests">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Tests du groupe
                </div>
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="tests-table">
                        <thead>
                            <th>#</th>
                            <th>Titre</th>
							<th>Groupe</th>
							<th>Profésseur Référant</th>
							<th>Etat</th>
							<th>Date Création</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
							<?php $i=1; ?>
							<?php foreach($tests as $test):?>
								<tr>
									<td><span id="test_<?= $test->id_test; ?>"><?= $i ?></span></td>
									<td><?= $test->titre_test; ?></td>
									<td><?= $test->group->num_grpe; ?></td>
									<td><?= $test->teacher->nom; ?> <?= $test->teacher->prenom; ?></td>
									<td><?= ($test->bActif) ? "Session en cours"  : (($test->bFini) ? "Session terminée" : "Session pas débutée")?></td>
									<td><?= $test->date_test; ?></td>
									<td>
                                        <a href="//<?= Url::connect("test_view", $test->id_test, Text::slug($test->titre_test)); ?>" class="btn btn-info">Voir</a>
                                    </td>
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