<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Test extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function recupTest($id_grpe){
    	$test = $this->db->select("SELECT * FROM test WHERE id_grpe = :id AND bActif = 1", array(
			":id" => $id_grpe
		));

    	return $test;
    }

    public function getTests() : array {
        $tmp = $this->db->select("SELECT * FROM test", array());
		foreach($tmp as $t){
			$t->teacher = $this->db->select("SELECT * from professeur WHERE id_prof = :id_prof", array(
				":id_prof" => $t->id_prof,
			))[0];
			
			$t->group = (new Group())->getGroup($t->id_grpe);
		}
		return $tmp;
    }
	
	public function getTestsOfGroup($id) : array {
        $tmp = $this->db->select("SELECT * FROM test WHERE id_grpe = :check", array(
			":check" => $id
		));
		foreach($tmp as $t){
			$t->teacher = $this->db->select("SELECT * from professeur WHERE id_prof = :id_prof", array(
				":id_prof" => $t->id_prof,
			))[0];
			
			$t->group = (new Group())->getGroup($t->id_grpe);
		}
		return $tmp;
    }
	
    public function getTest($id, string $column = "id_test"){
        $result = $this->db->select("SELECT * FROM test WHERE $column = :check", array(
            ":check" => $id
        ));
		
		if($result != null) $result = $result[0];
		
		$result->teacher = $this->db->select("SELECT * from professeur WHERE id_prof = :id_prof", array(
			":id_prof" => $result->id_prof,
		))[0];
		
		$result->group = (new Group())->getGroup($result->id_grpe);
		
		$result->questions = $this->db->select("SELECT * from question q, qcm c WHERE q.id_quest = c.id_quest AND c.id_test = :check", array(
			":check" => $result->id_test
		));
		
		$nbRepCorrect = 0;
		$nbRepTotal = 0;
		
		foreach($result->questions as $question){
			$question->reponses = $this->db->select("SELECT * from reponse WHERE id_quest = :id", array(
				":id" => $question->id_quest
			));
			
			$nbBonneRep = 0;
			$nbBonneRepAttendu = 0;
			$nbRep = 0;
			
			foreach($question->reponses as $response){
				if($response->bvalide == 1) $nbBonneRepAttendu++;
				$response->nb = $this->db->select("SELECT count(*) as nb from resultat WHERE id_quest = :idq AND id_rep = :idr AND id_test = :idt", array(
					":idq" => $question->id_quest,
					":idr" => $response->id_rep,
					":idt" => $id,
				))[0]->nb;
				
			}
			
			if($question->bmultiple == 0){
				$nbRepTotal++;
			}else{
				$nbRepCorrect += $nbBonneRep;
				$nbRepTotal += $nbBonneRepAttendu;
			}
			
			$question->nbBonneRep = $nbBonneRep;
			$question->nbRep = $nbRep;
			$question->nbBonneRepAttendu = $nbBonneRepAttendu;
		}
		
        return $result;
    }
	
	public function getQuestionsId($id){
		$q = $this->db->select("SELECT * from question q, qcm c WHERE q.id_quest = c.id_quest AND c.id_test = :check", array(
			":check" => $id
		));
		
		$tmp = [];
		foreach($q as $t){
			$tmp[] = $t->id_quest;
		}
		return $tmp;
	}

    public function exist($id, string $column = "id_test") : bool {
        $check = $this->db->select("SELECT $column FROM test WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$resultats = $this->db->select("SELECT * FROM resultat WHERE id_test = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($resultats as $r){
			(new Resultat())->del($r->id_res);
		}
		
		$qcms = $this->db->select("SELECT * FROM qcm WHERE id_test = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($qcms as $q){
			(new Question())->delQcmAssoc($q->id_qcm);
		}
		
		$bilans = $this->db->select("SELECT * FROM bilan WHERE id_test = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($bilans as $b){
			(new Bilan())->del($b->id_bilan);
		}
		
		$check = $this->db->delete("test", array(
			"id_test" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("test", $datas);
    }
	
	
	public function update($datas, $id) : int {
		return $this->db->update("test", $datas, array(
			"id_test" => $id
		));
    }
}
