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
            Question : <?= $question->titre; ?>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur la question
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_question");?>" method="post">
						<div id="result"></div>
						<div class="form-group">
                            <label for="name">Titre</label>
                            <input class="form-control" id="titre" name="titre" type="text" required value="<?= $question->titre;?>" />
                        </div>
						<div class="form-group">
                            <label for="name">Description</label>
                            <textarea class="form-control" id="summernote" name="desc" required><?= $question->texte;?></textarea>
                        </div>
						<div class="form-group" id="theme_input">
							<label for="name">Theme</label>
							<select class="form-control" name="theme_id" id="theme_id">
								<option value="-1">Non repertoriée</option>
								<?php foreach($themes as $theme) : ?>
									<option value="<?= $theme->id_theme; ?>" <?=($theme->id_theme == $question->id_theme) ? "selected" : "";?>><?= $theme->titre_theme; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="hidden" name="id" value="<?= $question->id_quest; ?>" />
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
                </div>
            </div>
        </div>
		</div>
		<div class="row">
		 <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Réponses
                </div>
                <div class="widget-content padded clearfix">
                    <form class="ajax-form" data-url="//<?= Url::connect("ajax_edit_question_answer");?>" method="post">
						<div id="result"></div>
						<div id="reps">
							<?php $i = 0;?>
							<?php foreach($question->reponses as $reponse): ?>
							<div class="row" id="rep_<?= $i;?>">
								<div class="col-md-10 col-sm-9">
								<div class="input-group">
									<span class="input-group-addon">
										<input type="checkbox" name="selected[<?= $i;?>]" <?=($reponse->bvalide == 1) ? "checked" : "";?> style="display: inline !important;" />
									</span>
									<input type="text" name="rep[<?= $i;?>]" class="form-control" value="<?= $reponse->texte_rep;?>" />
								</div>
								</div>
								<div class="col-md-2 col-sm-3 text-center">
								<button class="btn btn-danger" type="button" onclick="removeRep(<?= $i;?>)">
									<i class="fa fa-trash-o" style="margin-right: 0px;"></i> Supprimer
								</button>
								</div>
							</div>
							<input type="hidden" name="ids[<?= $i;?>]" value="<?= $reponse->id_rep;?>" />
							<?php $i++; ?>
							<?php endforeach; ?>
						</div>
						<input type="hidden" name="id" value="<?= $question->id_quest; ?>" />
						<br>
						<button class="btn btn-primary pull-right" type="submit">Modifier</button>
                    </form>
					<button onclick="addRep()" class="btn btn-info">Ajouter une réponse</button>
                </div>
            </div>
        </div>
	</div>
</div>