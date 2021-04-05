<?php
namespace App\Controller;

use Core\Controller;
use Web\Url;
use Web\Request;
use Usage\Arr;
use Session\Session;
use Usage\Validate;
use Security\Password;

class AuthController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->loadModel("User");
    }

    public function auth(){
		$this->view->set(["title" => "Authentification"]);
        $this->view->render(["folder" => "auth", "file" => "login"]);
    }
	
	public function register(){
		$this->view->set(["title" => "Inscription"]);
        $this->view->render(["folder" => "auth", "file" => "register"]);
    }
	
	public function account(){
		if($this->session->readUser("type") == "professeur"){
			$c = new TeacherController();
			$c->account();
		}else{
			$c = new StudentController();
			$c->account();
		}
		exit();
	}
	
	public function logout(){
		$this->loadModel("Online");
		
		if($this->session->readUser("type") != "professeur")
			$this->Online->logout($this->session->readUser("id_etu"));
		
		$this->session->destroy();
		
		Url::redirect(Url::connect("auth"), true, 301);
		exit();
    }
	
	public function ajax_auth(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_login" => Request::post("username"),
                "input_password" => Request::post("password"),
                "input_type" => Request::post("type"),
			);
			
			$errors = "";
			
			if($tmp["input_login"] == "" || $tmp["input_password"] == "" || $tmp["input_type"] == ""){
				$errors = "Merci de remplir tous les champs";
			} else{
				if($tmp["input_type"] != "etudiant" && $tmp["input_type"] != "professeur"){
					$errors = "Veuilliez selectionner un mode";
				}else{
					if($tmp["input_login"] == "" || $tmp["input_password"] == "" ){ 
						$errors = "Merci de remplir tous les champs";
					}else{
						if(!$this->User->isRegistered($tmp["input_login"], $tmp["input_type"])){
							$errors = "Ce compte n'existe pas.";
						}else{
							if(!Password::verify($tmp["input_password"], $this->User->getPassword($tmp["input_login"], $tmp["input_type"]))){
								$errors .= "Le mot de passe est incorrect";
							}
						}
					}
				}
			}
			
			if($errors != ""){
                $data["result"]["error"] = $errors;
            }else{
				$datas = $this->User->getDatas($tmp["input_type"], $tmp["input_login"]);
				
				foreach((array) $datas as $k => $v){
					$this->session->writeUser($k, $v);
				}
				$this->session->writeUser("connected", true);
				$this->session->writeUser("type", $tmp["input_type"]);
				
				
				$data["result"]["success"] = "Bonjour " . $datas->nom . " vous êtes désormais connecté !";
				$data["result"]["redirect"] = "/";
            }
			echo json_encode($data);
		}else{
			$data["result"]["error"] = "Cette méthode n'est pas autorisée pour cette requete";
			echo json_encode($data);
		}
	}
	
	public function ajax_register(){
		$data = [];
		
		$this->loadModel("Student");
		
		if(Request::isPost()){
            $tmp = array(
				"input_name" => Request::post("name"),
				"input_surname" => Request::post("surname"),
				"input_email" => Request::post("email"),
				"input_username" => Request::post("username"),
				"input_password" => Request::post("password"),
				"input_rpassword" => Request::post("rpassword"),
				"input_sexe" => Request::post("sexe"),
			);
			
			$errors = "";
			
			
			foreach($tmp as $input_name => $input_value){
                if($input_value == "" && $input_name != "input_checkbox") {
                    $errors .= str_replace("input_", "", $input_name) .", ";
                }
            }
			
			if($tmp["input_sexe"] != "F" && $tmp["input_sexe"] != "M"){
				$data["result"]["error"] = "Votre sexe est incorrect";
				echo json_encode($data);
				exit();
			}
			
			if (strlen($tmp["input_username"]) < 3 && $tmp["input_username"] != "") {
				$data["result"]["error"] = "Votre nom d'utilisateur est trop court";
				echo json_encode($data);
				exit();
			}else{
				if($this->Student->exist($tmp["input_username"], "login_etu")){
					$data["result"]["error"] = "Ce nom d'utilisateur est déjà utilisé";
					echo json_encode($data);
					exit();
				}
			}

			if (!Validate::validateEmail($tmp["input_email"]) && $tmp["input_email"] != "") {
				$data["result"]["error"] = "Merci de renseigner une adresse email valide.";
				echo json_encode($data);
				exit();
			} else {
				if($this->Student->exist($tmp["input_email"], "email")){
					$data["result"]["error"] = "Cette email est déjà utilisée";
					echo json_encode($data);
					exit();
				}
			}
			
			if (strlen($tmp["input_password"]) < 5 && $tmp["input_password"] != "") {
				$data["result"]["error"] = "Votre mot de passe est trop court.";
				echo json_encode($data);
				exit();
			} else if ($tmp["input_password"] != $tmp["input_rpassword"]) {
				$data["result"]["error"] = "Votre mot de passe et sa confirmation ne correspondent pas.";
				echo json_encode($data);
				exit();
			}
			
			if($errors != ""){
                $data["result"]["error"] = "Merci de vérifier les champs suivant : " . $errors;
            }else{
				
				$this->Student->add([
					"genre" => $tmp["input_sexe"],
					"nom" => $tmp["input_name"],
					"prenom" => $tmp["input_surname"],
					"email" => $tmp["input_email"],
					"login_etu" => $tmp["input_username"],
					"pass_etu" => Password::make($tmp["input_password"]),
					"matricule" => "",
					"bConnect" => 0,
				]);
				
				$data["result"]["success"] = "Vous êtes désormais inscrit !";
				$data["result"]["redirect"] = "/";
            }
			echo json_encode($data);
		}else{
			$data["result"]["error"] = "Cette méthode n'est pas autorisée pour cette requete";
			echo json_encode($data);
		}
	}
}