<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Bilan extends Model {

    public function __construct()
    {
        parent::__construct();
    }
	
	public function getDetailedBilansOfStudent($id){
		$tests = $this->db->select("SELECT * from test t, resultat r WHERE t.id_test = r.id_test AND r.id_etu = :check GROUP BY t.id_test", array(
			":check" => $id
		));
		
		$tmp = [];
		
		foreach($tests as $test){
			$tmp[] = $this->getDetailedBilanOfStudent($id, $test->id_test);
		}
		
		return $tmp;
	}
	
	public function getDetailedBilanOfStudent($id, $testId){
		$test = $this->db->select("SELECT * from test t, resultat r WHERE t.id_test = r.id_test AND t.id_test = :id_test AND r.id_etu = :check", array(
			":check" => $id,
			":id_test" => $testId,
		));
		
		if(count($test) == 0){
			return null;
		}else{
			$test = $test[0];
		}
		
		$test->questions = $this->db->select("SELECT * from question q, qcm c WHERE q.id_quest = c.id_quest AND c.id_test = :check", array(
			":check" => $test->id_test
		));
		
		$nbRepCorrect = 0;
		$nbRepTotal = 0;
		
		$test->group = (new Group())->getGroup($test->id_grpe);
		
		foreach($test->questions as $question){
			$question->reponses = $this->db->select("SELECT * from reponse WHERE id_quest = :id", array(
				":id" => $question->id_quest
			));
			
			$nbBonneRep = 0;
			$nbBonneRepAttendu = 0;
			$nbRep = 0;
			
			foreach($question->reponses as $response){
				$check = $this->db->select("SELECT * from resultat WHERE id_test = :id_test AND id_etu = :id_etu AND id_quest = :id_quest AND id_rep = :id_rep", array(
					":id_test" => $test->id_test,
					":id_etu" => $id,
					":id_quest" => $question->id_quest,
					":id_rep" => $response->id_rep
				));
				
				$response->selected = (count($check) > 0) ? true : false;
				if($response->selected) $nbRep++;
				if($response->bvalide == 1) $nbBonneRepAttendu++;
				if($response->bvalide == 1 && $response->selected) $nbBonneRep++;
			}
			
			$question->correct = ($nbBonneRep == $nbBonneRepAttendu) ? true : false;
			if($question->bmultiple == 0){
				if($question->correct) $nbRepCorrect++;
				$nbRepTotal++;
			}else{
				$nbRepCorrect += $nbBonneRep;
				$nbRepTotal += $nbBonneRepAttendu;
			}
			
			$question->nbBonneRep = $nbBonneRep;
			$question->nbRep = $nbRep;
			$question->nbBonneRepAttendu = $nbBonneRepAttendu;
		}
		
		$test->note = $nbRepCorrect;
		$test->total = $nbRepTotal;
		
		$test->teacher = $this->db->select("SELECT * from professeur WHERE id_prof = :id_prof", array(
			":id_prof" => $test->id_prof,
		))[0];
		
		$test->bilan = $this->db->select("SELECT * from bilan WHERE id_test = :id_test AND id_etu = :id_etu", array(
			":id_test" => $test->id_test,
			":id_etu" => $id,
		));
			
		$test->bilan = (count($test->bilan) == 0) ? null : $test->bilan[0];
		
		return $test;
	}
	
	
	public function getBilansOfStudent($id){
		$tests = $this->db->select("SELECT * from test t, resultat r WHERE t.id_test = r.id_test AND r.id_etu = :check GROUP BY t.id_test", array(
			":check" => $id
		));
		
		foreach($tests as $test){
			$test->bilan = $this->db->select("SELECT * from bilan WHERE id_test = :id_test AND id_etu = :id_etu", array(
				":id_test" => $test->id_test,
				":id_etu" => $id,
			));
			
			$test->bilan = (count($test->bilan) == 0) ? null : $test->bilan[0];
			$test->total = $this->getDetailedBilanOfStudent($id, $test->id_test)->total;
			
			$test->teacher = $this->db->select("SELECT * from professeur WHERE id_prof = :id_prof", array(
				":id_prof" => $test->id_prof,
			))[0];
		}
		
		return $tests;
	}
	
	public function getBilanOfTest($testId){
		$test = (new Test())->getTest($testId);
		
		$test->nbAbs = 0;
		$test->minNote = 999;
		$test->maxNote = 0;
		$test->avg = 0;
		
		$students = (new Group())->getStudentsInGroup($test->id_grpe);
		foreach($students as $student){
			$student->detail = $this->getDetailedBilanOfStudent($student->id_etu, $testId);
			if($student->detail == null) $test->nbAbs++;
			else{
				if($test->minNote > $student->detail->note) 
					$test->minNote = $student->detail->note;
				
				if($test->maxNote < $student->detail->note) 
					$test->maxNote = $student->detail->note;
				
				$test->avg += $student->detail->note;
				$test->total = $student->detail->total;
			}
		}
		
		$test->avg = $test->avg / (count($students) - $test->nbAbs);
		$test->students = $students;
		return $test;
	}

	public function del(int $id) : int {
		$check = $this->db->delete("bilan", array(
			"id_bilan" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("bilan", $datas);
    }
	

}
