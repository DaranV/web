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
            Theme : <?= $theme->titre_theme; ?>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="add">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le thème
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_theme");?>" method="post">
						<div id="result"></div>
                        <div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="titre" name="titre" type="text" required value="<?= $theme->titre_theme;?>" />
                        </div>
						<div class="form-group">
                            <label for="name">Description</label>
                            <textarea class="form-control" id="desc" name="desc" required><?= $theme->desc_theme;?></textarea>
                        </div>
						<input type="hidden" name="id" value="<?= $theme->id_theme; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8" id="classes">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Question du thème
                </div>
                <div class="widget-content padded clearfix">
                    <table class="table table-bordered table-striped" id="questions-table">
                        <thead>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
							<?php foreach($questions as $question):?>
								<tr>
									<td><span id="question_<?= $question->id_quest; ?>"><?= $i ?></span></td>
									<td><?= $question->titre; ?></td>
									<td><?= $question->texte; ?></td>
									<td>
                                        <button onclick="del('//<?=Url::connect("ajax_remove_question_theme", $theme->id_theme, $question->id_quest);?>')" class="btn btn-danger">Supprimer</button>
                                        <a href="//<?=Url::connect("question_view", $question->id_quest, Text::slug($question->titre));?>" class="btn btn-info">Voir</a>
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
                    <i class="fa fa-shield"></i>Ajouter une question au thème
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_add_question_theme");?>" method="post">
						<div id="result"></div>
						<div class="form-group" id="question_input">
							<label for="name">Question</label>
							<select class="form-control" name="question_id" id="question_id">
								<?php foreach($questionsA as $question) : ?>
									<option value="<?= $question->id_quest; ?>"><?= $question->titre; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="hidden" name="id" value="<?= $theme->id_theme; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>