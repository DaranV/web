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