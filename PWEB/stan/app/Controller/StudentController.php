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

class StudentController extends Controller{

    public function __construct()
    {
        parent::__construct();
        $this->loadModel(["Question", "Resultat", "Student", "Group", "Test", "Bilan"]);
		$this->view->setLayout("student");
    }
		
	public function index(){
		$this->view->js(["js/live.js?v=" . time()]);
		$this->view->css(["css/question.css"]);
		$this->view->set([
			"title" => "Accueil"
		]);
		
		$this->view->render(["folder" => "student", "file" => "home"]);
	}
	
	public function account(){
		$this->view->set([
			"title" => "Mon Compte",
			"bilans" => $this->Bilan->getBilansOfStudent($this->session->readUser("id_etu"))
		]);
		$this->view->js(["js/account.js?v=1"]);
		$this->view->css(["teacher/css/datatables.css"]);
		
		$this->view->render(["folder" => "student", "file" => "account"]);
	}
	
	public function view_bilan($testId){
		if($this->Test->exist($testId)){
			$student = $this->Student->getStudent($this->session->readUser("id_etu"));
			$bilan = $this->Bilan->getDetailedBilanOfStudent($this->session->readUser("id_etu"), $testId);
			if($bilan != null){
				$this->view->set(["title" => "Détail du test", "bilan" => $bilan]);
				$this->view->css(["css/question.css"]);
				$this->view->render(["folder" => "student", "file" => "detail_test"]);
			}else{
				$this->e404();
			}
		}else{
			$this->e404();
		}
	}
	
    public function ajax_live(){
		$data = [];
		
		if($this->Group->studentHasGroup($this->session->readUser("id_etu"))){
			$group = $this->Group->getGroup($this->Group->getGroupOf($this->session->readUser("id_etu"))->id_grpe);
			
			$test = $this->Test->recupTest($group->id_grpe);
			if($test == null){
				$data["state"] = "notest";
			}else{
				$test = $this->Test->getTest($test[0]->id_test);
				
				$questionAffichee = null;
				
				foreach($test->questions as $question){
					if($question->bAutorise == 1) {
						$questionAffichee = $question;
						shuffle($questionAffichee->reponses);
						break;
					}
				}
				
				if($questionAffichee == null){
					$data["state"] = "waitingprof";
					$data["data"]["test"] = $test;
				}else{
					if($this->Resultat->studentHasAnswered($test->id_test, $this->session->readUser("id_etu"), $questionAffichee->id_quest)){
						$data["state"] = "alreadyreplied";
						$data["data"]["test"] = $test;
					}else{
						$data["state"] = "answering";
						$data["data"]["question"] = $questionAffichee;
						$data["data"]["test"] = $test;
					}
				}
			}
		}else{
			$data["state"] = "nogroup";
		}
		
		echo json_encode($data);
		
    }

    public function ajax_sendAnswer(){
        $data = [];

        if(Request::isPost()){
            $tmp = array(
                "rep" => @$_POST["rep"],
                "id_test" => Request::post("test_id"),
                "id_quest" => Request::post("question_id"),

            );
        
			$errors = "";

			if(@$tmp["rep"] == ""){
				$errors = "Veuillez répondre";
			}
	 
			if(!$this->Test->exist($tmp["id_test"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
	 
			if(!$this->Question->exist($tmp["id_quest"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
	 
			if($errors != ""){
				$data["result"]["error"] = $errors;
				echo json_encode($data);
			}
			else{
				if(is_array($tmp["rep"])){
					foreach($tmp["rep"] as $rep){
						$tab = array(
							"id_test" => $tmp["id_test"],
							"id_etu" => $this->session->readUser("id_etu"),
							"id_quest" => $tmp["id_quest"],
							"id_rep" => $rep,
						);

						$datas = $this->Resultat->add($tab);
					}
				}else{
					$tab = array(
						"id_test" => $tmp["id_test"],
						"id_etu" => $this->session->readUser("id_etu"),
						"id_quest" => $tmp["id_quest"],
						"id_rep" => $tmp["rep"],
					);

					$datas = $this->Resultat->add($tab);
				}
				
				$data["result"]["success"] = "Votre réponse a bien été enregistré";
				echo json_encode($data);
			}
		}
		else{
            $data["result"]["error"] = "Cette méthode n'est pas autorisée pour cette requete";
            echo json_encode($data);
        }
    }
	
	public function ajax_set_group(){
		$data = [];

        if(Request::isPost()){
			$group = Request::post("code");
			
			if($group == ""){
				$data["result"]["error"] = "Vous devez entrer un code de groupe.";
				echo json_encode($data);
				exit();
			}
			
			if(!$this->Group->exist($group, "code")){
				$data["result"]["error"] = "Ce code n'est relié a aucun groupe.";
				echo json_encode($data);
				exit();
			}
			
			$g = $this->Group->getGroup($group, "code");
			$this->Group->insetGroupOf($g->id_grpe, $this->session->readUser("id_etu"));
			
			$data["result"]["success"] = "Vous faites désormais partit du groupe " . $g->num_grpe . " !";
			$data["result"]["redirect"] = "/";
			echo json_encode($data);
		}
		else{
            $data["result"]["error"] = "Cette méthode n'est pas autorisée pour cette requete";
            echo json_encode($data);
        }
	}
	
	public function ajax_edit(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_nom" => Request::post("nom"),
                "input_prenom" => Request::post("prenom"),
                "input_email" => Request::post("email"),
                "input_username" => Request::post("username"),
                "input_id" => $this->session->readUser("id_etu"),
			);
			
			$errors = "";
			
			if(!$this->Student->exist($tmp["input_id"])){
				$data["result"]["error"] = "Cet etudiant n'existe pas";
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
				$student = $this->Student->getStudent($tmp["input_id"]);
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
				
				if($student->login_etu != $tmp["input_username"]){
					$modif = true;
				}
				
				if($modif){
					$this->Student->update([
						"nom" => $tmp["input_nom"],
						"prenom" => $tmp["input_prenom"],
						"email" => $tmp["input_email"],
						"login_etu" => $tmp["input_username"]
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
                "input_id" => $this->session->readUser("id_etu"),
			);
			
			$errors = "";
			
			if(!$this->Student->exist($tmp["input_id"])){
				$data["result"]["error"] = "Cet etudiant n'existe pas";
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
					$this->Student->update([
						"pass_etu" => Password::make($tmp["input_password"])
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
