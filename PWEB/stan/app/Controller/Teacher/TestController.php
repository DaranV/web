<?php
namespace App\Controller\Teacher;

use Core\Controller;
use Web\Url;
use Usage\Arr;
use Web\Request;
use Usage\Validate;
use Usage\Number;
use Usage\Text;

class TestController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("teacher");
		$this->loadModel("Test");
    }

    public function index(){
		$this->view->set(["title" => "Tests | Administration", "tests" => $this->Test->getTests()]);
		$this->view->css(["teacher/css/datatables.css"]);
		$this->view->js(["teacher/page/tests.page.js?v=5"]);
        $this->view->render(["folder" => "teacher", "file" => "tests"]);
    }
	
	public function add(){
		$this->loadModel("Group");
		$this->view->set(["title" => "Créer un test | Administration", "groups" => $this->Group->getGroups()]);
		$this->view->css(["teacher/css/datatables.css"]);
		$this->view->js(["teacher/page/test_add.page.js?v=" . time()]);
        $this->view->render(["folder" => "teacher", "file" => "test_add"]);
	}
	
	public function ajax_delete($id){
		$data = [];
		
		if(Request::isGet()){
			
			$errors = "";
			
			if(!$this->Test->exist($id)){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$this->Test->del($id);
				$data["result"]["success"] = "Le test a bien été supprimé";
				$data["result"]["id"] = $id;
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function view($id, $slug){
		if($this->Test->exist($id)){
			$test = $this->Test->getTest($id);
			if($test->bFini == 1){
				Url::redirect(Url::connect("test_ended_view", $id, $slug), true, 301);
			}else if($test->bActif == 1){
				Url::redirect(Url::connect("test_live_editor_view", $id, $slug), true, 301);
			}
			else{
				Url::redirect(Url::connect("test_editor_view", $id, $slug), true, 301);
			}
		}else{
			$this->e404();
		}
	}
	
	public function editor_view($id, $slug){
		if($this->Test->exist($id)){
			$this->loadModel("Group");
			$this->loadModel("Theme");
			$this->view->set(["title" => "Edition du test | Administration", "sQuestions" => $this->Test->getQuestionsId($id), "groups" => $this->Group->getGroups(), "questions" => $this->Theme->getFormatedQuestions(), "test" => $this->Test->getTest($id)]);
			$this->view->css(["teacher/css/datatables.css", "css/question.css"]);
			$this->view->js(["https://cdn.ckeditor.com/4.13.0/full/ckeditor.js", "teacher/page/test.page.js?v=" . time()]);
			$this->view->render(["folder" => "teacher", "file" => "test"]);
		}else{
			$this->e404();
		}
	}
	
	public function live_editor_view($id, $slug){
		if($this->Test->exist($id)){
			$this->loadModel("Group");
			$this->loadModel("Theme");
			$this->view->set(["title" => "Edition du test en live | Administration", "sQuestions" => $this->Test->getQuestionsId($id), "groups" => $this->Group->getGroups(), "questions" => $this->Theme->getFormatedQuestions(), "test" => $this->Test->getTest($id)]);
			$this->view->css(["teacher/css/datatables.css", "css/question.css"]);
			$this->view->js(["teacher/js/jquery.easy-pie-chart.js", "teacher/js/jquery.sparkline.min.js", "teacher/js/morris.min.js", "teacher/js/raphael.min.js", "https://cdn.ckeditor.com/4.13.0/full/ckeditor.js", "teacher/page/test.page.js?v=" . time(), "teacher/page/testlive.page.js?v=" . time()]);
			$this->view->render(["folder" => "teacher", "file" => "test_live"]);
		}else{
			$this->e404();
		}
	}
	
	public function ended_view($id, $slug){
		if($this->Test->exist($id)){
			$this->loadModel("Bilan");
			$this->view->set(["title" => "Résultat du test | Administration", "test" => $this->Test->getTest($id), "bilan" => $this->Bilan->getBilanOfTest($id)]);
			$this->view->css(["teacher/css/datatables.css","css/question.css"]);
			$this->view->js(["teacher/js/jquery.easy-pie-chart.js", "teacher/js/jquery.sparkline.min.js", "teacher/js/morris.min.js", "teacher/js/raphael.min.js", "teacher/page/test.page.js?v=" . time()]);
			$this->view->render(["folder" => "teacher", "file" => "test_ended"]);
		}else{
			$this->e404();
		}
	}
	
	public function test_preview($type, $id){
		if($this->Test->exist($id)){
			if($type != 0 && $type != 1){
				$this->e404();
				exit();
			}
			
			$this->loadModel("Group");
			$this->loadModel("Theme");
			$this->view->set(["type" => $type, "sQuestions" => $this->Test->getQuestionsId($id), "groups" => $this->Group->getGroups(), "questions" => $this->Theme->getFormatedQuestions(), "test" => $this->Test->getTest($id)]);
			$this->view->autoRender(false);
			$this->view->render(["folder" => "teacher", "file" => "test_preview"]);
		}else{
			$this->e404();
		}
	}
	
	public function test_preview_questions($id){
		if($this->Test->exist($id)){
			$this->loadModel("Group");
			$this->loadModel("Theme");
			$this->view->set(["sQuestions" => $this->Test->getQuestionsId($id), "groups" => $this->Group->getGroups(), "questions" => $this->Theme->getFormatedQuestions(), "test" => $this->Test->getTest($id)]);
			$this->view->autoRender(false);
			$this->view->render(["folder" => "teacher", "file" => "test_preview_questions"]);
		}else{
			$this->e404();
		}
	}
	
	public function test_start($id, $slug){
		if($this->Test->exist($id)){
			$this->Test->update([
				"bActif" => 1,
				"bFini" => 0,
			], $id);
			
			Url::redirect(Url::connect("test_live_editor_view", $id, $slug), true, 301);
		}else{
			$this->e404();
		}
	}
	
	public function test_stop($id, $slug){
		if($this->Test->exist($id)){
			$this->Test->update([
				"bActif" => 0,
				"bFini" => 1,
			], $id);
			
			$this->loadModel("Bilan");
			$bilan = $this->Bilan->getBilanOfTest($id);
			
			foreach($bilan->students as $student){
				if($student->detail != null){
					$this->Bilan->add([
						"date_bilan" => date("Y-m-d"),
						"note_test" => $student->detail->note,
						"id_etu" => $student->id_etu,
						"id_test" => $id,
					]);
				}
			}
			
			Url::redirect(Url::connect("test_ended_view", $id, $slug), true, 301);
		}else{
			$this->e404();
		}
	}
	
	public function ajax_add(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_name" => Request::post("titre"),
                "input_groupe" => Request::post("group_id"),
			);
			
			$errors = "";
			
			if($this->Test->exist($tmp["input_name"], "titre_test")){
				$data["result"]["error"] = "Un test porte déjà ce titre";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			$this->loadModel("Group");
			if(!$this->Group->exist($tmp["input_groupe"])){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				
            }else{
				$id = $this->Test->add([
					"titre_test" => $tmp["input_name"],
					"id_grpe" => $tmp["input_groupe"],
					"date_test" => date("Y-m-d"),
					"id_prof" => $this->session->readUser("id_prof")
				]);
				$data["result"]["success"] = "Le test a bien été ajouté";
				$data["result"]["redirect"] = "//" . Url::connect("test_view", $id, Text::slug($tmp["input_name"]));
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_add_question(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("test_id"),
                "input_question" => Request::post("question_id"),
			);
			
			$errors = "";
			
			if(!$this->Test->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Question");
			
			if(!$this->Question->exist($tmp["input_question"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$q = $this->Question->getQuestion($tmp["input_question"]);
				$this->Question->removeQuestionQcmAssoc($tmp["input_id"], $tmp["input_question"]);
				$this->Question->addQuestionQcmAssoc($tmp["input_id"], $tmp["input_question"]);
				$data["result"]["success"] = "Question ajoutée avec succès";
				$data["result"]["data"] = $q;
				$data["result"]["data"]->url = "//" . Url::connect("question_view", $q->id_quest, Text::slug($q->titre));
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_remove_question(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("test_id"),
                "input_question" => Request::post("question_id"),
			);
			
			$errors = "";
			
			if(!$this->Test->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Question");
			
			if(!$this->Question->exist($tmp["input_question"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$q = $this->Question->getQuestion($tmp["input_question"]);
				$this->Question->removeQuestionQcmAssoc($tmp["input_id"], $tmp["input_question"]);
				$data["result"]["success"] = "Question ajoutée avec succès";
				$data["result"]["data"] = $q;
				$data["result"]["data"]->url = "//" . Url::connect("question_view", $q->id_quest, Text::slug($q->titre));
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
                "input_group" => Request::post("group_id"),
                "input_id" => Request::post("id"),
			);
			
			$errors = "";
			
			if(!$this->Test->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Group");
		
			if(!$this->Group->exist($tmp["input_group"])){
				$data["result"]["error"] = "Ce groupe n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			
			if($errors != ""){
                $data["result"]["error"] = "Please verify these fields : <br>" . $errors;
            }else{
				$test = $this->Test->getTest($tmp["input_id"]);
				$modif = false;
				
                if($test->titre_test != $tmp["input_titre"]){
					$modif = true;
					if($this->Test->exist($tmp["input_titre"], "titre_test")){
						$data["result"]["error"] = "Un test porte déjà ce nom";
						echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
						exit();
					}
				}
				
				if($test->id_grpe != $tmp["input_group"]){
					$modif = true;
				}
				
				if($modif){
					$this->Test->update([
						"titre_test" => $tmp["input_titre"],
						"id_grpe" => $tmp["input_group"],
					], $tmp["input_id"]);
					$data["result"]["success"] = "Test mis à jour avec succès";
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
	
	public function ajax_list_onlines($id){
		$data = [];
		
		if(Request::isGet()){
			if(!$this->Test->exist($id)){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel(["Group", "Resultat"]);
			
			$test = $this->Test->getTest($id);
			$tmp = [];
			
			$questionAffichee = null;
				
			foreach($test->questions as $question){
				if($question->bAutorise == 1) {
					$questionAffichee = $question;
					shuffle($questionAffichee->reponses);
					break;
				}
			}
			
			foreach($this->Group->getStudentsConnectedInGroup($test->id_grpe) as $t){
				$state = "En attente d'une question";
				
				if($questionAffichee != null){
					if($this->Resultat->studentHasAnswered($test->id_test, $t->id_etu, $questionAffichee->id_quest)){
						$state = "a répondu";
					}else{
						$state = "est entrain de répondre";
					}
				}
				
				$tmp[] = [
					"nom" => $t->nom,
					"prenom" => $t->prenom,
					"id" => $t->id_etu,
					"state" => $state
				];
			}
			
			$data["onlines"] = $tmp;
			
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_start_question(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("test_id"),
                "input_question" => Request::post("question_id"),
			);
			
			$errors = "";
			
			if(!$this->Test->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Question");
			
			if(!$this->Question->exist($tmp["input_question"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$q = $this->Question->getQuestion($tmp["input_question"]);
				
				$this->Question->updateQuestionQcmAssoc([
					"bAutorise" => 1,
					"bBloque" => 0,
					"bAnnule" => 0,
				], $tmp["input_question"], $tmp["input_id"]);
				
				$data["result"]["success"] = "success";
				$data["result"]["data"] = $q;
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_stop_question(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("test_id"),
                "input_question" => Request::post("question_id"),
                "input_type" => Request::post("type"),
			);
			
			$errors = "";
			
			if(!$this->Test->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Question");
			
			if(!$this->Question->exist($tmp["input_question"])){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($tmp["input_type"] != "closed" && $tmp["input_type"] != "annulated"){
				$data["result"]["error"] = "Le type d'arret est incorrect.";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			} 
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$q = $this->Question->getQuestion($tmp["input_question"]);
				
				$this->Question->updateQuestionQcmAssoc([
					"bAutorise" => 0,
					"bBloque" => ($tmp["input_type"] == "closed") ? 1 : 0,
					"bAnnule" => ($tmp["input_type"] == "annulated") ? 1 : 0,
				], $tmp["input_question"], $tmp["input_id"]);
				
				$data["result"]["success"] = "success";
				$data["result"]["data"] = $q;
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	private function in_array_r($item , $array){
		return preg_match('/"'.preg_quote($item, '/').'"/i' , json_encode($array));
	}
	
	public function ajax_test_live_stats($id){
		$data = [];
		
		if(Request::isGet()){
			if(!$this->Test->exist($id)){
				$data["result"]["error"] = "Ce test n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel(["Group", "Resultat"]);
			
			$test = $this->Test->getTest($id);
			$tmp = [];
			
			$questionAffichee = null;
				
			foreach($test->questions as $question){
				if($question->bAutorise == 1) {
					$questionAffichee = $question;
					break;
				}
			}
			
			foreach($this->Group->getStudentsConnectedInGroup($test->id_grpe) as $t){
				$state = "en attente d'une question";
				
				if($questionAffichee != null){
					if($this->Resultat->studentHasAnswered($test->id_test, $t->id_etu, $questionAffichee->id_quest)){
						$state = "a répondu";
					}else{
						$state = "est entrain de répondre";
					}
				}
				
				$tmp[] = [
					"nom" => $t->nom,
					"prenom" => $t->prenom,
					"id" => $t->id_etu,
					"state" => $state,
					"online" => true
				];
			}
			
			foreach($this->Group->getStudentsInGroup($test->id_grpe) as $t){
				if(!$this->in_array_r($t->id_etu, $tmp)){
					$tmp[] = [
						"nom" => $t->nom,
						"prenom" => $t->prenom,
						"id" => $t->id_etu,
						"state" => "non connecté",
						"online" => false
					];
				}
			}
			
			$colors = [];
			$values = [];
			
			$data["students"] = $tmp;
			$data["questions"] = array_map(function ($elem){ 
				return [
					"titre" => $elem->titre,
					"id" => $elem->id_quest,
					"state" => ($elem->bAutorise == 1) ? "selected" : (($elem->bBloque == 1) ? "closed" : (($elem->bAnnule) ? "annulated" : "nothing"))
				]; 
			}, $test->questions);
			
			$data["selected"] = $questionAffichee;
			
			if($questionAffichee != null){
				$i = 0;
				
				foreach($questionAffichee->reponses as $elem){
					$color = "#" . substr(md5($elem->texte_rep), 0, 6);
					$colors[$i] = $color;
					$values[$i++] = $elem->nb;
				}
				
				$data["selected"]->chart = [
					"values" => $values,
					"colors" => $colors
				];
			}
			
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
}