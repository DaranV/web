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
            Liste des questions
        </h1>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="widget-container fluid-height clearfix">
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="questions-table">
                        <thead>
                            <th>#</th>
                            <th>Titre</th>
							<th>Thème</th>
							<th>Choix multiple</th>
							<th>Nombre de réponse</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
							<?php $i=1; ?>
							<?php foreach($questions as $question):?>
								<tr>
									<td><span id="question_<?= $question->id_quest; ?>"><?= $i ?></span></td>
									<td><?= $question->titre; ?></td>
									<td><?= $question->theme; ?></td>
									<td><?= ($question->bmultiple) ? "oui" : "non"; ?></td>
									<td><?= $question->nbRep; ?></td>
									<td>
                                        <button onclick="del('//<?=Url::connect("ajax_delete_question", $question->id_quest);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="//<?= Url::connect("question_view", $question->id_quest, Text::slug($question->titre));?>" class="btn btn-info">Voir</a>
                                    </td>
                                </tr>
								<?php $i++; ?>
							<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
		<div class="col-md-4">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Ajouter une question
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?php echo Url::connect("ajax_add_question");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="name" name="name" type="text" required />
                        </div>
                        <button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>