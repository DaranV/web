<?php
namespace App\Controller\Teacher;

use Core\Controller;
use Web\Url;
use Usage\Arr;
use Web\Request;
use Usage\Validate;
use Usage\Number;
use Usage\Text;

class QuestionController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("teacher");
		$this->loadModel("Question");
    }

    public function index(){
		$this->view->set(["title" => "Questions | Administration", "questions" => $this->Question->getQuestions()]);
		$this->view->css(["teacher/css/datatables.css"]);
		$this->view->js(["teacher/page/questions.page.js"]);
        $this->view->render(["folder" => "teacher", "file" => "questions"]);
    }
	
	public function view($id, $slug){
		if($this->Question->exist($id)){
			$this->loadModel("Theme");
			$this->view->set(["title" => "Edition d'une question | Administration", "question" => $this->Question->getQuestion($id), "themes" => $this->Theme->getThemes()]);
			$this->view->css(["teacher/css/datatables.css", "http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css"]);
			$this->view->js(["https://cdn.ckeditor.com/4.13.0/full/ckeditor.js", "teacher/page/question.page.js?v=" . time()]);
			$this->view->render(["folder" => "teacher", "file" => "question"]);
		}else{
			$this->e404();
		}
	}
	
	public function ajax_delete($id){
		$data = [];
		
		if(Request::isGet()){
			
			$errors = "";
			
			if(!$this->Question->exist($id)){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$this->Question->del($id);
				$data["result"]["success"] = "La question a bien été supprimée";
				$data["result"]["id"] = $id;
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_edit_answers(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("id"),
                "no_redirect" => Request::post("no_redirect"),
			);
			
			$errors = "";
			
			if(!$this->Question->exist($tmp["input_id"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$reps = [];
				$nbSelected = 0;
				
				if(@$_POST["rep"] == null){
					$data["result"]["success"] = "Aucune réponse ajoutée";
					echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
					exit();
				}
				
				foreach($_POST["rep"] as $k => $v){
					if($v != ""){
						$t = [
							"rep" => $v,
							"selected" => (@$_POST["selected"][$k] != null) ? 1 : 0,
							"id" => (@$_POST["ids"][$k] != null) ? $_POST["ids"][$k] : -1,
						];
						
						if($t["selected"]) $nbSelected++;
						$reps[] = $t;
					}
				}
				
				if($nbSelected == 0){
					$data["result"]["error"] = "Vous devez selectionner au-moins une bonne réponse";
					echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
					exit();
				}
				
				foreach($reps as $rep){
					if($rep["id"] != -1){
						$this->Question->updateRep([
							"texte_rep" => $rep["rep"],
							"bvalide" => $rep["selected"]
						], $rep["id"]);
					}else{
						$this->Question->addRep([
							"texte_rep" => $rep["rep"],
							"bvalide" => $rep["selected"],
							"id_quest" => $tmp["input_id"]
						]);
					}
				}
				
				if(@$_POST["del"] != null){
					foreach($_POST["del"] as $delId){
						$this->Question->delRep($delId);
					}
				}
				
				$this->Question->update([
					"bmultiple" => ($nbSelected > 1) ? 1 : 0
				],$tmp["input_id"]);
				
				$question = $this->Question->getQuestion($tmp["input_id"]);
				
				$data["result"]["success"] = "La question a bien été modifiée !";
				if($tmp['no_redirect'] == "")
					$data["result"]["redirect"] = "//" . Url::connect("question_view", $tmp["input_id"], Text::slug($question->titre));
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
                "input_titre" => Request::post("titre"),
                "input_desc" => $_POST['desc'],
                "input_id" => Request::post("id"),
                "input_theme" => Request::post("theme_id"),
			);
			
			$errors = "";
			
			if(!$this->Question->exist($tmp["input_id"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Theme");
			
			if($tmp["input_theme"] != -1 && !$this->Theme->exist($tmp["input_theme"])){
				$data["result"]["error"] = "Ce theme n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($tmp["input_titre"] == ""){
				$data["result"]["error"] = "Vous devez indiquer un titre";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
                $data["result"]["error"] = "Please verify these fields : <br>" . $errors;
            }else{
				$question = $this->Question->getQuestion($tmp["input_id"]);
				$modif = false;
				
                if($question->titre != $tmp["input_titre"]){
					$modif = true;
					if($this->Question->exist($tmp["input_titre"], "titre")){
						$data["result"]["error"] = "Une question porte déjà ce nom";
						echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
						exit();
					}
				}
				
				if($question->texte != $tmp["input_desc"]){
					$modif = true;
				}
				
				if($question->id_theme != $tmp["input_theme"]){
					$modif = true;
				}
				
				if($modif){
					$this->Question->update([
						"titre" => $tmp["input_titre"],
						"texte" => $tmp["input_desc"],
						"id_theme" => $tmp["input_theme"]
					], $tmp["input_id"]);
					$data["result"]["success"] = "Question mise à jour avec succèes";
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
	
	public function ajax_add(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_name" => Request::post("name"),
                "no_redirect" => Request::post("no_redirect"),
			);
			
			$errors = "";
			
			if($this->Question->exist($tmp["input_name"], "titre")){
				$data["result"]["error"] = "Une question porte déjà ce nom";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				
            }else{
				$id = $this->Question->add([
					"titre" => $tmp["input_name"],
					"texte" => "",
					"bmultiple" => 0,
					"id_theme" => -1
				]);
				
				$data["result"]["success"] = "La question a bien été ajoutée";
				$data["result"]["id"] = $id;
				
				if($tmp['no_redirect'] == "")
					$data["result"]["redirect"] = "//" . Url::connect("question_view", $id, Text::slug($tmp["input_name"]));
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajaw_view($id){
		$data = [];
		
		if(Request::isGet()){
			$errors = "";
			
			if(!$this->Question->exist($id)){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$data["datas"] = $this->Question->getQuestion($id);
		
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
}