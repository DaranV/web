<?php
namespace App\Controller;

use Core\Controller;
use Web\Url;
use Usage\Arr;
use Web\Request;
use Usage\Validate;
use Usage\Number;
use Usage\Text;
use Security\Password;

class TeacherController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("teacher");
		$this->loadModel("Prof");
    }

    public function index(){
		$this->view->set(["title" => "Administration"]);
        $this->view->render(["folder" => "teacher", "file" => "index"]);
    }

	public function account(){
		$this->view->set(["title" => "Mon Compte"]);
        $this->view->render(["folder" => "teacher", "file" => "account"]);
    }
	
	public function ajax_edit(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_nom" => Request::post("nom"),
                "input_prenom" => Request::post("prenom"),
                "input_email" => Request::post("email"),
                "input_username" => Request::post("username"),
                "input_id" => $this->session->readUser("id_prof"),
			);
			
			$errors = "";
			
			if(!$this->Prof->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce professeur n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			foreach($tmp as $input_name => $input_value){
                if($input_value == "" && $input_name != "input_checkbox") {
                    $errors .= str_replace("input_", "", $input_name) .", ";
                }
            }
			
			if($errors != ""){
                $data["result"]["error"] = "Please verify these fields : <br>" . $errors;
            }else{
				$student = $this->Prof->getProf($tmp["input_id"]);
				$modif = false;
				
                if($student->nom != $tmp["input_nom"]){
					$modif = true;
				}
				
				if($student->prenom != $tmp["input_prenom"]){
					$modif = true;
				}
				
				if($student->email != $tmp["input_email"]){
					$modif = true;
				}
				
				if($student->login_prof != $tmp["input_username"]){
					$modif = true;
				}
				
				if($modif){
					$this->Prof->update([
						"nom" => $tmp["input_nom"],
						"prenom" => $tmp["input_prenom"],
						"email" => $tmp["input_email"],
						"login_prof" => $tmp["input_username"]
					], $tmp["input_id"]);
					$data["result"]["success"] = "Compte mis à jour avec succès";
				}else{
					$data["result"]["success"] = "Aucune modification apportée";
				}
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_edit_password(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_password" => Request::post("password"),
                "input_rpassword" => Request::post("rpassword"),
                "input_id" => $this->session->readUser("id_prof"),
			);
			
			$errors = "";
			
			if(!$this->Prof->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce professeur n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			foreach($tmp as $input_name => $input_value){
                if($input_value == "" && $input_name != "input_checkbox") {
                    $errors .= str_replace("input_", "", $input_name) .", ";
                }
            }
			
			if($errors != ""){
                $data["result"]["error"] = "Please verify these fields : <br>" . $errors;
            }else{
				if($tmp["input_password"] != $tmp["input_rpassword"]){
					$data["result"]["error"] = "Le mot de passe et sa confirmation ne correspondent pas";
					echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
					exit();
				}else if(strlen($tmp["input_password"]) < 5){
					$data["result"]["error"] = "Le mot de passe est trop court";
					echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
					exit();
				}else{
					$this->Prof->update([
						"pass_prof" => Password::make($tmp["input_password"])
					], $tmp["input_id"]);
					$data["result"]["success"] = "Mot de passe modifié avec succès";
				}
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
}