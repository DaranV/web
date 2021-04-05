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
           Test : 
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le test
                </div>
                <div class="widget-content padded clearfix">
					<div class="form-group">
						<label for="name">Etudiant</label>
						<span class="form-control"><?= $student->nom; ?> <?= $student->prenom; ?></span>
					</div>
					<div class="form-group">
						<label for="name">Groupe</label>
						<span class="form-control"><?= $bilan->group->num_grpe; ?></span>
					</div>
					<div class="form-group">
						<label for="name">Profésseur référant</label>
						<span class="form-control"><?= $bilan->teacher->nom; ?> <?= $bilan->teacher->prenom; ?></span>
					</div>
					<div class="form-group">
						<label for="name">Note</label>
						<span class="form-control"><?= $bilan->note; ?> / <?= $bilan->total; ?></span>
					</div>
					<div class="form-group">
						<label for="name">Date Bilan</label>
						<span class="form-control"><?= (@$bilan->bilan->date_bilan == "") ? "Date Inconnue" : $bilan->bilan->date_bilan; ?></span>
					</div>
					<a class="btn btn-info" href="//<?= Url::connect("test_view", $bilan->id_test, Text::slug($bilan->titre_test)); ?>">Voir les résultats du groupe</a>
				</div>
            </div>
        </div>
		<div class="col-md-8" id="edit">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Réponses de l'étudiant
                </div>
				<div class="widget-content padded clearfix">
					<?php foreach($bilan->questions as $question): ?>
					<div>
						<h4><?= $question->titre;?> <small>( <?= $question->nbBonneRepAttendu; ?> points)</small></h4>
						<h5><?= $question->texte;?>
						<?php if($question->nbRep == 0): ?>
							<small style="color: red;">(L'étudiant n'a pas répondu à cette question)</small>
						<?php endif;?> 
						<span class="pull-right" style="<?= (($question->correct || $question->nbBonneRep > 0)) ? "color: green;" : "color: red;";?>"><?= ($question->correct) ? "+" . $question->nbBonneRepAttendu : (($question->nbBonneRep > 0) ? "+" . ($question->nbBonneRepAttendu - $question->nbBonneRep) : "-" . $question->nbBonneRepAttendu); ?> points</span></h5>
						<?php foreach($question->reponses as $rep): ?>
							<div class="form-group q-form">
								<input disabled type="radio" id="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" name="q_<?=$question->id_quest;?><?=($question->bmultiple == 1) ? "_"  . $rep->id_rep: "";?>" <?= ($rep->selected) ? "checked" : "";?>/>
								<label for="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" style="<?= ($question->correct && $rep->selected) ? "color: green;" : (($question->correct) ? "color: #666666;" : (($rep->bvalide && $rep->selected) ? "color: green" : (($rep->bvalide) ? "color: green;" : "color: red")));?>"><span></span><?= $rep->texte_rep;?></label>
							</div>
						<?php endforeach; ?>
						<?php if($question->bmultiple == 1): ?>
						<br>
						<small>* Plusieures réponses possibles</small>
						<?php endif;?>
					</div><br><br>
					<?php endforeach;?>
					<h3 class="pull-right">Note : <strong><?= $bilan->note; ?> / <?= $bilan->total; ?></strong></h3>
					<hr>
					<small> 
						- Les points bleus symbolisent les réponses sélectionnées par l'étudiant <br> 
						- Les bonnes et mauvaises réponses sont respectivement en vert et rouge.
					</small>
				</div>
            </div>
        </div>
    </div>
</div>