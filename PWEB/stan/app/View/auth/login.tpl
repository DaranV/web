<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;

$stan = Stan::getInstance();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Se connecter</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/animate/animate.css">	
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/select2/select2.min.css">	
	<link rel="stylesheet" type="text/css" href="assets/auth/vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/css/util.css">
	<link rel="stylesheet" type="text/css" href="assets/auth/css/main.css">
	<style>
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
.input-box {
  position: relative;
}

.input-box input {
  border-style: none;
  background: transparent;
  border-bottom: 1px solid white;
  width: 100%;
  position: relative;
  outline: none;
  padding: 10px 0;
  color: white;
  font-size: 18px;
  margin-bottom: 30px;
}

.input-box label {
  color: white;
  position: absolute;
  padding: 10px 0;
  top: 0;
  left: 0;
  pointer-events: none;
  transition: 0.5s;
}

.input-box input:focus ~ label,
.input-box input:valid ~ label {
  color: #8ea3f2bd;
  font-size: 16px;
  top: -20px;
  transition: 0.5s;
}

label, span {
	color: #555555;
}
	</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-t-85 p-b-20">
				<form class="login100-form validate-form ajax-form" data-url="//<?php echo Url::connect("ajax_auth");?>" method="post">
					<span class="login100-form-title m-b-85">
						Se connecter
					</span>
					
					<div id="result"></div>
					
					<div class="wrap-input100 validate-input m-b-35" data-validate = "Champs requis">
						<input class="input100" type="text" name="username">
						<span class="focus-input100" data-placeholder="Nom d'utilisateur ou email"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-50" data-validate="cChamps requis">
						<input class="input100" type="password" name="password">
						<span class="focus-input100" data-placeholder="Mot de passe"></span>
					</div>
					<div class="m-b-50">
						<span>Vous êtes ?</span><br><br>
						<input type="checkbox" name="type" value="etudiant" id="ck1" />
						<label for="ck1">Etudiant</label>
						<br>
						<input type="checkbox" name="type" value="professeur" id="ck2"/>
						<label for="ck2">Professeur</label>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Se connecter
						</button>
					</div>

					<ul class="login-more p-t-20">
						<!--<li>
							<a href="#" class="txt2">
								Mot de passe oublié?
							</a>
						</li>-->

						<li>
							<a href="//<?= Url::connect("register");?>" class="txt2">
								Créer un compte
							</a>
						</li>
					</ul>
				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
	<script src="assets/auth/vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="assets/auth/vendor/animsition/js/animsition.min.js"></script>
	<script src="assets/auth/vendor/bootstrap/js/popper.js"></script>
	<script src="assets/auth/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="assets/auth/vendor/select2/select2.min.js"></script>
	<script src="assets/auth/vendor/daterangepicker/moment.min.js"></script>
	<script src="assets/auth/vendor/daterangepicker/daterangepicker.js"></script>
	<script src="assets/auth/vendor/countdowntime/countdowntime.js"></script>
	<script src="assets/auth/js/main.js"></script>
	<script src="assets/js/ajax.builder.js"></script>  
	<script>
	$("#ck1").change(function() {
		if(this.checked) {
			console.log("aaa");
			$("#ck2").prop('checked', false);
		}
	});
	
	$("#ck2").change(function() {
		if(this.checked) {
			$("#ck1").prop('checked', false);
		}
	});
	</script>
</body>
</html>