<?php
use Web\Url;
use Web\Assets;
use Usage\Text;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Liste des tests
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
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
                                        <button onclick="del('//<?=Url::connect("ajax_delete_test", $test->id_test);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="/test/<?= $test->id_test; ?>/<?= Text::slug($test->titre_test); ?>" class="btn btn-info">Voir</a>
                                    </td>
                                </tr>
								<?php $i++; ?>
							<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
			<br>
			 <a href="//<?= Url::connect("test_add");?>" class="btn btn-info">Créer un test</a>
        </div>
    </div>
</div>