<?php
namespace App\Controller\Teacher;

use Core\Controller;
use Web\Url;
use Usage\Arr;
use Web\Request;
use Usage\Validate;
use Usage\Number;
use Usage\Text;

class ThemeController extends Controller{

    public function __construct()
    {
        parent::__construct();
		$this->view->setLayout("teacher");
		$this->loadModel("Theme");
    }

    public function index(){
		$this->view->set(["title" => "Thèmes | Administration", "themes" => $this->Theme->getThemes()]);
		$this->view->css(["teacher/css/datatables.css"]);
		$this->view->js(["teacher/page/themes.page.js"]);
        $this->view->render(["folder" => "teacher", "file" => "themes"]);
    }
	
	public function view($id, $code){
		if($this->Theme->exist($id)){
			$this->loadModel("Question");
			$this->view->set(["title" => "Edition du thème | Administration", "theme" => $this->Theme->getTheme($id), "questions" => $this->Question->getQuestionsByTheme($id), "questionsA" => $this->Question->getQuestionsNotInTheme($id)]);
			$this->view->css(["teacher/css/datatables.css"]);
			$this->view->js(["teacher/page/theme.page.js"]);
			$this->view->render(["folder" => "teacher", "file" => "theme"]);
		}else{
			$this->e404();
		}
	}
	
	public function ajax_add(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_name" => Request::post("name"),
                "input_desc" => Request::post("desc"),
			);
			
			$errors = "";
			
			if($this->Theme->exist($tmp["input_name"], "titre_theme")){
				$data["result"]["error"] = "Un thème porte déjà ce numéro";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				
            }else{
				$id = $this->Theme->add([
					"titre_theme" => $tmp["input_name"],
					"desc_theme" => $tmp["input_desc"]
				]);
				$data["result"]["success"] = "Le thème a bien été ajouté";
				$data["result"]["redirect"] = "//" . Url::connect("theme_index");
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
			
			if(!$this->Theme->exist($id)){
				$data["result"]["error"] = "Ce thème n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$this->Theme->del($id);
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
                "input_titre" => Request::post("titre"),
                "input_desc" => Request::post("desc"),
                "input_id" => Request::post("id"),
			);
			
			$errors = "";
			
			if(!$this->Theme->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce thème n'existe pas";
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
				$theme = $this->Theme->getTheme($tmp["input_id"]);
				$modif = false;
				
                if($theme->titre_theme != $tmp["input_titre"]){
					$modif = true;
					if($this->Theme->exist($tmp["input_titre"], "titre_theme")){
						$data["result"]["error"] = "Un thème porte déjà ce nom";
						echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
						exit();
					}
				}
				
				if($theme->desc_theme != $tmp["input_desc"]){
					$modif = true;
				}
				
				if($modif){
					$this->Theme->update([
						"titre_theme" => $tmp["input_titre"],
						"desc_theme" => $tmp["input_desc"],
					], $tmp["input_id"]);
					$data["result"]["success"] = "Thème mis à jour avec succès";
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
	
	public function ajax_add_question(){
		$data = [];
		
		if(Request::isPost()){
            $tmp = array(
                "input_id" => Request::post("id"),
                "input_question" => Request::post("question_id"),
			);
			
			$errors = "";
			
			if(!$this->Theme->exist($tmp["input_id"])){
				$data["result"]["error"] = "Ce thème n'existe pas";
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
				$theme = $this->Theme->getTheme($tmp["input_id"]);
				$this->Question->update([
					"id_theme" => $theme->id_theme
				], $tmp["input_question"]);
				$data["result"]["success"] = "Question ajoutée avec succès";
				$data["result"]["redirect"] = "//" . Url::connect("theme_view", $tmp["input_id"], Text::slug($theme->titre_theme));
            }
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}else{
			$data["result"]["error"] = "This method is not allowed for this request";
			echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
		}
	}
	
	public function ajax_remove_question($themeId, $questionId){
		$data = [];
		
		if(Request::isGet()){
			
			$errors = "";
			
			if(!$this->Theme->exist($themeId)){
				$data["result"]["error"] = "Ce thème n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
			
			$this->loadModel("Question");
			
			if(!$this->Question->exist($questionId)){
				$data["result"]["error"] = "Cette question n'existe pas";
				echo json_encode(Arr::merge($data, ["version" => "1.0.0"]));
				exit();
			}
		
			if($errors != ""){
				$data["result"]["error"] = "Merci de vérifier les champs suivant : <br>" . $errors;
            }else{
				$theme = $this->Theme->getTheme($themeId);
				$question = $this->Question->getQuestion($questionId);
				
				if($question->id_theme == $theme->id_theme){
					$this->Question->update([
						"id_theme" => -1
					], $question->id_quest);
					$data["result"]["success"] = "Question retirée avec succès";
					$data["result"]["id"] = $questionId;
				}else{
					$data["result"]["error"] = "Cette question ne possède pas ce thème";
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