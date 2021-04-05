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
<style>
.q-form {
	border-left: 1px solid black;
	padding-left: 10px;
	margin-left: 10px !important;
	margin-bottom: 0px !important;
}
</style>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            Test : <?= $test->titre_test;?>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="add">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le test
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_test");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="titre" name="titre" type="text" required value="<?= $test->titre_test;?>" />
                        </div>
						<div class="form-group" id="group_input">
							<label for="name">Groupe</label>
							<select class="form-control" name="group_id" id="group_id">
								<?php foreach($groups as $group) : ?>
									<option <?= ($test->id_grpe == $group->id_grpe) ? "selected" : "";?> value="<?= $group->id_grpe; ?>"><?= $group->num_grpe; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="hidden" value="<?= $test->id_test;?>" name="id" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
					<a href="//<?= Url::connect("test_start", $test->id_test, Text::slug($test->titre_test)); ?>" class="btn btn-success pull-right">Lancer le test</a>
                </div>
            </div>
        </div>
		<div class="col-md-8">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Ajouter des questions
                </div>
                <div class="widget-content padded clearfix" id="preview3">
					 <div class="panel-group" id="accordion">
					  <?php foreach($questions as $k => $v): ?>
					  <?php 
					  $z = 0;
					  foreach($v["questions"] as $question){
						if(in_array($question->id_quest, $sQuestions)) $z++;
					  }
					  ?>
					  <div class="panel panel-default">
						<div class="panel-heading">
						  <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= $k; ?>">
								<?= $v["title"]; ?> <span class="pull-right"><span id="<?= $k; ?>_selected_nb"><?= $z; ?></span> / <span id="<?= $k; ?>_total_nb"><?= count($v["questions"]) ;?></span> <span id="<?= $k; ?>_loader" style="display: none;"><i class="fa fa-spin fa-spinner"></i></span></span>
							</a>
						  </h4>
						</div>
						<div id="collapse-<?= $k; ?>" class="panel-collapse collapse in">
						  <div class="panel-body">
							<?php foreach($v["questions"] as $question): ?>
								<div class="checkbox">
									<label><input <?= (in_array($question->id_quest, $sQuestions)) ? "checked" : "" ;?> type="checkbox" data-theme-id="<?= $k; ?>" data-question-id="<?= $question->id_quest;?>" style="display: inline;"><?= $question->titre;?></label><span class="pull-right"><a onclick="del('//<?=Url::connect("ajax_delete_question", $question->id_quest);?>')"><i class="fa fa-trash"></i></a> - <a onclick="edit(<?= $question->id_quest;?>)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span>
								</div>
							<?php endforeach; ?>
						  </div>
						</div>
					  </div>
					  <?php endforeach; ?>
					</div>
					<div id="result_question"></div>					
                </div>
            </div>
        </div>
	</div>
	<div class="row">
		<div class="col-md-4" style="display: none;">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Créer une question
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?php echo Url::connect("ajax_add_question_2");?>" method="post">
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
		
		<div class="col-md-6">
			<div class="widget-container fluid-height clearfix" id="addQuestionForm">
                <div class="heading">
                    <i class="fa fa-shield"></i>Ajouter une question
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?php echo Url::connect("ajax_add_question");?>" method="post" data-callback="afterCreateQuestion">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="name" name="name" type="text" required />
                        </div>
						<input type="hidden" name="no_redirect" value="true" />
                        <button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
            <div class="widget-container fluid-height clearfix" style="display: none;" id="editQuestionForm" >
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur la question
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_question");?>" data-callback="afterEditQuestion" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="titre" name="titre" type="text" required />
                        </div>
						<div class="form-group">
                            <label for="name">Description</label>
                            <textarea class="form-control" id="summernote" name="desc" required></textarea>
                        </div>
						<div class="form-group" id="theme_input">
							<label for="name">Theme</label>
							<select class="form-control" name="theme_id" id="theme_id">
								<option value="-1">Non repertoriée</option>
							</select>
						</div>
						 <input type="hidden" name="id" />
						 <input type="hidden" name="no_redirect" value="true" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
		
		<div class="col-md-6">
            <div class="widget-container fluid-height clearfix" style="display: none;" id="answerForm" >
                <div class="heading">
                    <i class="fa fa-shield"></i>Réponses
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_question_answer");?>" data-callback="afterEditAnswers" method="post">
						<div id="result"></div>
						<div id="reps">
						</div>
						<br>
						<input type="hidden" name="id"  />
						<input type="hidden" name="no_redirect" value="true" />
						<button class="btn btn-primary pull-right" type="submit">Terminer</button>
                    </form>
					<button onclick="addRep()" class="btn btn-info">Ajouter une réponse</button>
                </div>
            </div>
        </div>
	</div>
	<div class="row">
        <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Contenu du test
                </div>
                <div class="widget-content padded clearfix">
					<ul class="nav nav-tabs">
					  <li class="active"><a data-toggle="tab" href="#table">Liste des questions</a></li>
					  <li><a data-toggle="tab" href="#preview1">Prévisualisation (sans correction)</a></li>
					  <li><a data-toggle="tab" href="#preview2">Prévisualisation (avec correction)</a></li>
					</ul>

					<div class="tab-content">
					  <div id="table" class="tab-pane fade in active">
						<br>
						<table class="table table-bordered table-striped" id="questions-table">
                        <thead>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
							<?php foreach($test->questions as $question):?>
								<tr>
									<td><span id="question_<?= $question->id_quest; ?>"><?= $i ?></span></td>
									<td><?= $question->titre; ?></td>
									<td><?= $question->texte; ?></td>
									<td>
                                        <a href="//<?=Url::connect("question_view", $question->id_quest, Text::slug($question->titre));?>" class="btn btn-info">Voir</a>
                                    </td>
                                </tr>
								<?php $i++; ?>
							<?php endforeach; ?>
							</tbody>
						</table>
						</div>
						<div id="preview1" class="tab-pane fade">
							<br>
							<?php foreach($test->questions as $question): ?>
							<div>
								<h4><?= $question->titre;?> <small>( <?= $question->nbBonneRepAttendu; ?> points)</small></h4>
								<h5><?= $question->texte;?> </h5>
								<?php foreach($question->reponses as $rep): ?>
									<div class="form-group q-form">
										<input disabled type="radio" id="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" name="q_<?=$question->id_quest;?><?=($question->bmultiple == 1) ? "_"  . $rep->id_rep: "";?>" />
										<label for="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" style="color: #666666;"><span></span><?= $rep->texte_rep;?></label>
									</div>
								<?php endforeach; ?>
								<?php if($question->bmultiple == 1): ?>
								<br>
								<small>* Plusieures réponses possibles</small>
								<?php endif;?>
							</div><br><br>
							<?php endforeach;?>
						</div>
						<div id="preview2" class="tab-pane fade">
							<br>
							<?php foreach($test->questions as $question): ?>
							<div class="row">
								<div class="col-md-6">
									<h4><?= $question->titre;?> <small>( <?= $question->nbBonneRepAttendu; ?> points)</small></h4>
									<h5><?= $question->texte;?> </h5>
									<?php foreach($question->reponses as $rep): ?>
										<div class="form-group q-form">
											<input disabled type="radio" id="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" name="q_<?=$question->id_quest;?><?=($question->bmultiple == 1) ? "_"  . $rep->id_rep: "";?>" />
											<label for="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" style="<?= (($rep->bvalide) ? "color: green;" : "color: red");?>"><span></span><?= $rep->texte_rep;?></label>
										</div>
									<?php endforeach; ?>
									<?php if($question->bmultiple == 1): ?>
									<br>
									<small>* Plusieures réponses possibles</small>
									<?php endif;?>
								</div>
							</div><br><br>
							<hr>
							<?php endforeach;?>
							<hr>
							<small> 
								- Les bonnes et mauvaises réponses sont respectivement en vert et rouge.
							</small>
						</div>
					</div>
                </div>
            </div>
        </div>
	</div>
</div>

<script>
var AJAX_URL_ADD_QUESTION = "//<?= Url::connect("ajax_add_question_test");?>";
var AJAX_URL_REMOVE_QUESTION = "//<?= Url::connect("ajax_remove_question_test");?>";
var AJAX_URL_UPDATE_PREVIEW_0 = "//<?= Url::connect("test_preview", 0, $test->id_test);?>";
var AJAX_URL_UPDATE_PREVIEW_1 = "//<?= Url::connect("test_preview", 1, $test->id_test);?>";
var AJAX_URL_UPDATE_PREVIEW_3 = "//<?= Url::connect("test_preview_questions", $test->id_test);?>";
var AJAX_URL_QUESTION_DETAIL = "//<?= Url::connect("ajax_view_question", -1);?>";

var TEST_ID = "<?= $test->id_test;?>";

</script>