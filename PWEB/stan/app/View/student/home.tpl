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

[type="checkbox"]:not(:checked), [type="checkbox"]:checked {
  position: absolute;
  left: -9999px;
}

[type="checkbox"]:not(:checked) + label, [type="checkbox"]:checked + label {
  position: relative;
  padding-left: 1.95em;
  cursor: pointer;
}

/* checkbox aspect */
[type="checkbox"]:not(:checked) + label:before, [type="checkbox"]:checked + label:before {
  content: '';
  position: absolute;
  left: 0; top: 0;
  width: 1.25em; height: 1.25em;
  border: 2px solid #ccc;
  background: #fff;
  border-radius: 4px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
}

/* checked mark aspect */
[type="checkbox"]:not(:checked) + label:after, [type="checkbox"]:checked + label:after {
  content: '\2713\0020';
  position: absolute;
  top: .15em; left: .22em;
  font-size: 1.3em;
  line-height: 0.8;
  color: #09ad7e;
  transition: all .2s;
  font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', Arial;
}

/* checked mark aspect changes */
[type="checkbox"]:not(:checked) + label:after {
  opacity: 0;
  transform: scale(0);
}

[type="checkbox"]:checked + label:after {
  opacity: 1;
  transform: scale(1);
}

/* disabled checkbox */
[type="checkbox"]:disabled:not(:checked) + label:before, [type="checkbox"]:disabled:checked + label:before {
  box-shadow: none;
  border-color: #bbb;
  background-color: #ddd;
}

[type="checkbox"]:disabled:checked + label:after {
  color: #999;
}

[type="checkbox"]:disabled + label {
  color: #aaa;
}

/* accessibility */
[type="checkbox"]:checked:focus + label:before,
[type="checkbox"]:not(:checked):focus + label:before {
  border: 2px dotted blue;
}
</style>
<div class="container-fluid main-content text-center" style="display: flex;flex-direction: row;align-items: center;justify-content: center;">
	<div class="wrap-login100 p-t-85 p-b-20" id="state-notest">
		<span class="login100-form-title m-b-20">
			<i class="fa fa-clock-o fa-5x" aria-hidden="true"></i>
		</span>
		
		<p style="text-align: center;">Aucun test n'est actuellement disponible pour votre groupe.</p><br><br>
	</div>
	
	<div class="wrap-login100 p-t-85 p-b-20" id="state-waitingprof" style="display: none;">
		<span class="login100-form-title m-b-20">
			<i class="fa fa-clock-o fa-spin fa-5x" aria-hidden="true"></i>
		</span>
		
		<p style="text-align: center;">En attente du professeur</p><br><br>
	</div>
	
	<div class="wrap-login100 p-t-85 p-b-20" id="state-alreadyreplied" style="display: none;">
		<span class="login100-form-title m-b-20">
			<i class="fa fa-clock-o fa-5x" aria-hidden="true"></i>
		</span>
		
		<p style="text-align: center;">Vous avez déjà répondu, veuilliez attendre une autre question.</p><br><br>
	</div>
	
	<div class="wrap-login100 p-t-85 p-b-20" id="state-ended" style="display: none;">
		<span class="login100-form-title m-b-20">
			<i style="color: green;" class="fa fa-check-circle-o fa-5x" aria-hidden="true"></i>
		</span>
		
		<p style="text-align: center;">Le test est terminé ! <br><br><a id="link_bilan" href="//<?= Url::connect("view_my_bilan", -1);?>" class="btn btn-primary">Voir mes réponses</a></p> <br><br>
	</div>
	
	<div class="wrap-login100 p-t-85 p-b-20" id="state-answering" style="display: none;text-align: left; padding: 20px; min-width: 390px;">
		<div>
			<form class="ajax-form" data-url="//<?php echo Url::connect("ajax_sendAnswer");?>" method="post">
				<h4><span data-field="titre"></span> <small>( <span data-field="nbBonneRepAttendu"></span> points)</small></h4>
				<h5 data-field="texte"></h5>
				<div id="answers"></div>
				<div data-if="bmultiple" style="display: none;">
					<br>
					<small>* Plusieures réponses possibles</small>
				</div>
				<br>
				<div id="result"></div></br>
				<input type="hidden" name="test_id" />
				<input type="hidden" name="question_id" />
				<button type="submit" class="btn btn-primary">Valider</button>
			</form>
		</div>
		<hr>
		<div>
		Etat : <span data-field="state"></span>
		</div>
	</div>
	
</div>


<script>
var AJAX_LIVE_URL = "//<?= Url::connect("ajax_live");?>";
</script>