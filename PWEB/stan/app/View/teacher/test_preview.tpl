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

	<?php if($type == 0):?>
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
	<?php else: ?>
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
<?php endif; ?>