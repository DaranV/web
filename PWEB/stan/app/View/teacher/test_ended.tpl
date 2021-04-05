<?php
use Web\Url;
use Web\Assets;
use Usage\Text;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
?>
<style>
.q-form {
	border-left: 1px solid black;
	padding-left: 10px;
	margin-left: 10px !important;
	margin-bottom: 0px !important;
}

.pie-chart .pie-canvas {
    display: inline-block;
    vertical-align: middle;
}
</style>
<div class="container-fluid main-content">
    <div class="page-title">
        <h1>
            <?= $test->titre_test; ?> (du <?= $test->date_test; ?>)
        </h1>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Informations sur le thème
                </div>
                <div class="widget-content padded clearfix">
					<div class="form-group">
						<label for="name">Groupe</label>
						<span class="form-control"><?= $test->group->num_grpe; ?></span>
					</div>
					<div class="form-group">
						<label for="name">Profésseur référant</label>
						<span class="form-control"><?= $test->teacher->nom; ?> <?= $test->teacher->prenom; ?></span>
					</div>
                </div>
            </div>
        </div>
		<div class="col-lg-8">
      <div class="widget-container stats-container">
                <div class="col-md-4">
          <div class="number">
            <div class="fa fa-minus-circle" aria-hidden="true"></div>
			<?= $bilan->minNote;?> <small>/ <?= $bilan->total;?> </small>
           </div>
          <div class="text">
            Plus basse note
          </div>
        </div>
        <div class="col-md-4">
          <div class="number">
            <div class="fa fa-bandcamp"></div>
            <?= number_format($bilan->avg, 2, ',', ' ');?>      <small>/ <?= $bilan->total;?> </small>   </div>
          <div class="text">
            Moyenne du groupe
          </div>
        </div>
        <div class="col-md-4">
          <div class="number">
            <div class="fa fa-plus-circle"></div>
            <?= $bilan->maxNote;?> <small>/ <?= $bilan->total;?> </small>         </div>
          <div class="text">
            Plus haute note
          </div>
        </div>
              </div>
			  <div class="widget-container stats-container">
                <div class="col-md-4">
          <div class="number">
            <div class="fa fa-users" aria-hidden="true"></div>
            <?= count($bilan->students) - $bilan->nbAbs;?>         </div>
          <div class="text">
            Nombre de participant(s)
          </div>
        </div>
        <div class="col-md-4">
          <div class="number">
            <div class="fa fa-user-times"></div>
            <?= $bilan->nbAbs;?>          </div>
          <div class="text">
            Nombre d'absent(s)
          </div>
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
											<label for="q_<?=$question->id_quest;?>_<?=$rep->id_rep;?>" style="<?= (($rep->bvalide) ? "color: green;" : "color: red");?>"><span></span><?= $rep->texte_rep;?> (x<?= $rep->nb;?>)</label>
										</div>
									<?php endforeach; ?>
									<?php if($question->bmultiple == 1): ?>
									<br>
									<small>* Plusieures réponses possibles</small>
									<?php endif;?>
								</div>
								<div class="col-md-6">
								 <div class="pie-chart">
									<div class="pie-canvas" id="pie-chart-<?=$question->id_quest;?>"></div>
									<ul class="chart-key">
									  <?php 
										$colors = [];
										$values = [];
										$i = 0;
										foreach($question->reponses as $rep){
										  $color = "#" . random_color();
										  $colors[$i] = $color;
										  $values[$i++] = $rep->nb;
										  echo '<li><span style="background: ' . $color .  ';"></span>' . $rep->texte_rep . ' (' . $rep->nb . ')</li>';
										}
									  ?>
									  <span class="pie-generator" data-target="pie-chart-<?=$question->id_quest;?>" data-colors='<?=json_encode($colors);?>' data-values='<?=json_encode($values);?>'></span>
									</ul>
								  </div>
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
	<div class="row">
        <div class="col-md-12">
            <div class="widget-container fluid-height clearfix">
                <div class="heading">
                    <i class="fa fa-shield"></i>Résultats individuels
                </div>
                <div class="widget-content padded clearfix">
					<table class="table table-bordered table-striped" id="qcm-table">
                        <thead>
                            <th>#</th>
                            <th>Etudiant</th>
                            <th>Résultat</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            <?php $i = 1;?>
							<?php foreach($bilan->students as $student) : ?>
                                <tr>
                                    <td><span><?= $i ?></span></td>
                                    <td><?= $student->nom; ?> <?= $student->prenom; ?></td>
                                    <td><?= ($student->detail == null) ? "Résultat Inconnu" : $student->detail->note . "/" . $student->detail->total; ?></td>
                                    <td><?= ($student->detail == null) ? "Date Inconnue" : (($student->detail->bilan == NULL) ? "Date Inconnue" : $student->detail->date_test); ?></td>
									<td><a href="//<?= Url::connect("student_view_bilan", $student->id_etu, $test->id_test);?>" class="btn btn-info" <?= ($student->detail == null) ? "disabled" : ""; ?> >Voir les réponses de l'étudiant</a></td>
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