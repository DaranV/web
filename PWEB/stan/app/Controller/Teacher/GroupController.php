<?php
namespace App\Controller\Teacher;

use Core\Controller;
use Web\Url;
use Usage\Arr;
use Web\Request;
use Usage\Validate;
use Usage\Number;

class GroupController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("teacher");
		$this->loadModel("Group");
    }

    public function index(){
		$this->view->set(["title" => "Groupes | Administration", "groups" => $this->Group->getGroups()]);
		$this->view->css(["teacher/css/datatables.css"]);
		$this->view->js(["teacher/page/groups.page.js?v=2"]);
        $this->view->render(["folder" => "teacher", "file" => "groups"]);
    }
	
	public function view($id, $code){
		$this->loadModel("User");
		$this->loadModel("Test");
		if($this->Group->exist($id)){
			$this->view->set(["title" => "Edition Groupe  " . $code . " | Administration", "studentsA" => $this->Group->getStudentsNotInGroup($id), "group" => $this->Group->getGroup($id), "students" => $this->Group->getStudentsInGroup($id), "tests" => $this->Test->getTestsOfGroup($id)]);
			$this->view->css(["teacher/css/datatables.css"]);
			$this->view->js(["teacher/page/group.page.js?v=6"]);
			$this->view->render(["folder" => "teacher", "file" => "group"]);
		}else{
			$this->e404();
		}
	}

	public function ajax_add(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_name" => Request::post("name")
			);
			
			$errors = "";
			
			if($this->Group->exist($tmp["input_name"], "num_grpe")){
				$data["result"]["error"] = "Un groupe porte déjà ce numéro";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				
            }else{
				$id = $this->Group->add([
					"num_grpe" => $tmp["input_name"],
					"code" => Number::createKey(10, true)
				]);
				$data["result"]["success"] = "Le groupe a bien été ajouté";
				$data["result"]["redirect"] = "//" . Url::connect("group_index");
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_delete($id){
		$data = [];
		
		if(Request::isGet()){
			
			$errors = "";
			
			if(!$this->Group->exist($id)){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$this->Group->del($id);
				$data["result"]["success"] = "Le groupe a bien été supprimé";
				$data["result"]["id"] = $id;
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_edit(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_name" => Request::post("name"),
                "input_id" => Request::post("id"),
			);
			
			$errors = "";
			
			if(!$this->Group->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			foreach($tmp as $input_name => $input_value){
                if($input_value == "" && $input_name != "input_checkbox") {
                    $errors .= str_replace("input_", "", $input_name) .", ";
                }
            }
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$group = $this->Group->getGroup($tmp["input_id"]);
				if($group->num_grpe != $tmp["input_name"]){
					if($this->Group->exist($tmp["input_name"], "num_grpe")){
						$data["result"]["error"] = "Un groupe porte déjà ce numéro";
						echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
						exit();
					}else{
						$this->Group->update([
							"num_grpe" => $tmp["input_name"],
						], $group->id_grpe);
						$data["result"]["success"] = "Groupe modifié avec succès";
					}
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
	
	public function ajax_add_student(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("id"),
                "input_student" => Request::post("student_id"),
			);
			
			$errors = "";
			
			if(!$this->Group->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("User");
			
			if(!$this->User->isRegistered2("etudiant", $tmp["input_student"], "id_etu")){
				$data["result"]["error"] = "Cet étudiant n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$group = $this->Group->getGroup($tmp["input_id"]);
				if($this->Group->studentHasGroup($tmp["input_student"])){
					$g = $this->Group->getGroupOf($tmp["input_student"]);
					$this->Group->updateGroupOf($tmp["input_id"], $g->id_grpe, $tmp["input_student"]);
				}else{
					$this->Group->insetGroupOf($tmp["input_id"], $tmp["input_student"]);
				}
				$data["result"]["success"] = "Etudiant ajouté avec succès";
				$data["result"]["redirect"] = "//" . Url::connect("group_view", $tmp["input_id"], $group->num_grpe);
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_remove_student($groupId, $studentId){
		$data = [];
		
		if(Request::isGet()){
			
			$errors = "";
			
			if(!$this->Group->exist($groupId)){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("User");
			
			if(!$this->User->isRegistered2("etudiant", $studentId, "id_etu")){
				$data["result"]["error"] = "Cet étudiant n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				if($this->Group->studentHasGroup($studentId)){
					$g = $this->Group->getGroupOf($studentId);
					if($g->id_grpe == $groupId){
						$this->Group->removeGroupOf($studentId);
						$data["result"]["success"] = "Etudiant retiré du groupe avec succès";
						$data["result"]["id"] = $studentId;
					}else{
						$data["result"]["error"] = "Cet étudiant n'est pas dans ce groupe";
						echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
						exit();
					}
				}else{
					$data["result"]["error"] = "Cet étudiant est dans aucun groupe";
					echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
					exit();
				}
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
}