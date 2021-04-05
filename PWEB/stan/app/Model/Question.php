<?php
namespace App\Model;

use Core\Model;

class Question extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function afficherQuestions() {
        $questions = $this->db->select("SELECT * from question", array());

        foreach($questions as $question){
        	$question->reponses = $this->db->select("SELECT * from reponse WHERE id_quest = :id AND bvalide = 1", array(
        		":id" => $question->id_quest
        	));
        }

        return $questions;
    }

    public function recupQuestion(){
    	$questions = $this->db->select("SELECT * from question WHERE bSelect=1", array());

    	return $questions;
    }

   public function recupReponse(){
   		$reponse = $this->db->select("SELECT * FROM reponse, question WHERE reponse.id_quest = question.id_quest AND question.bSelect=1", array());

   		return $reponse;
   }
	
	public function getQuestions() : array {
        $tmp = $this->db->select("SELECT *, (SELECT count(*) from reponse r WHERE r.id_quest = question.id_quest) as nbRep FROM question", array());
		foreach($tmp as $t){
			if($t->id_theme == -1){
				$t->theme = "Non répertoriée";
			}else{
				$t->theme = (new Theme())->getTheme($t->id_theme)->titre_theme;
			}
		}
		return $tmp;
    }
	
	public function getQuestionsByTheme($id) : array {
        $tmp = $this->db->select("SELECT * FROM question WHERE id_theme = :check", array(
			":check" => $id
		));
		return $tmp;
    }
	
	public function getQuestionsNotInTheme($id) : array {
        $tmp = $this->db->select("SELECT * FROM question WHERE id_theme <> :check", array(
			":check" => $id
		));
		return $tmp;
    }
	
    public function getQuestion($id, string $column = "id_quest"){
        $result = $this->db->select("SELECT * FROM question WHERE $column = :check", array(
            ":check" => $id
        ));
		
		$result = $result[0];
		
		$result->reponses = $this->db->select("SELECT * from reponse WHERE id_quest = :id", array(
			":id" => $result->id_quest
		));
		
        return $result;
    }

    public function exist($id, string $column = "id_quest") : bool {
        $check = $this->db->select("SELECT $column FROM question WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$reponses = $this->db->select("SELECT * FROM reponse WHERE id_quest = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($reponses as $r){
			$this->db->delete("reponse", array(
				"id_rep" => $r->id_rep
			));
		}
		
		$resultats = $this->db->select("SELECT * FROM resultat WHERE id_quest = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($resultats as $r){
			(new Resultat())->del($r->id_res);
		}
		
		$check = $this->db->delete("question", array(
			"id_quest" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("question", $datas);
    }
	
	
	public function update($datas, $id) : int {
		return $this->db->update("question", $datas, array(
			"id_quest" => $id
		));
    }
	
	public function updateRep($datas, $id) : int {
		return $this->db->update("reponse", $datas, array(
			"id_rep" => $id
		));
    }
	
	public function addRep($datas) : string {
        return $this->db->insert("reponse", $datas);
    }
	
	public function delRep(int $id) : int {
		$check = $this->db->delete("reponse", array(
			"id_rep" => $id
		));

		return $check;
    }
	
	public function addQuestionQcmAssoc($test_id, $question_id) : string {
        return $this->db->insert("qcm", [
			"id_test" => $test_id,
			"id_quest" => $question_id,
		]);
    }
	
	public function updateQuestionQcmAssoc($datas, $question_id, $test_id) : int {
		return $this->db->update("qcm", $datas, [
			"id_test" => $test_id,
			"id_quest" => $question_id,
		]);
    }
	
	public function removeQuestionQcmAssoc($test_id, $question_id) : string {
       $check = $this->db->delete("qcm", array(
			"id_test" => $test_id,
			"id_quest" => $question_id,
		));

		return $check;
    }
	
	public function delQcmAssoc(int $id) : int {
		$check = $this->db->delete("qcm", array(
			"id_qcm" => $id
		));

		return $check;
    }
}