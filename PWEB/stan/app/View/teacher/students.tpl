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
            Liste des étudiants
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="students-table">
                        <thead>
                            <th>#</th>
                            <th>Nom</th>
							<th>Prénom</th>
							<th>Email</th>
							<th>Genre</th>
							<th>Matricule</th>
							<th>Groupe</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
							<?php $i=1; ?>
							<?php foreach($students as $student):?>
								<tr>
									<td><span id="student_<?= $student->id_etu; ?>"><?= $i ?></span></td>
									<td><?= $student->nom; ?></td>
									<td><?= $student->prenom; ?></td>
									<td><?= $student->email; ?></td>
									<td><?= $student->genre; ?></td>
									<td><?= $student->matricule; ?></td>
									<td><?= ($student->group == null) ? "Aucun groupe" : $student->group->num_grpe;?></td>
									<td>
                                        <button onclick="del('//<?=Url::connect("ajax_delete_student", $student->id_etu);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="/student/<?= $student->id_etu; ?>/<?= Text::slug($student->nom . '-' . $student->prenom); ?>" class="btn btn-info">Voir</a>
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