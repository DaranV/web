<?php
namespace App\Model;

use Core\Model;

class User extends Model {

    public function __construct()
    {
        parent::__construct();
    }

	public function getStudents() : array {
        return $this->db->select("SELECT * FROM etudiant", array());
    }

    public function isRegistered($check, $type) : bool {
		if($type == "etudiant"){
			return $this->isRegistered2("etudiant", $check, "login_etu");
		}else if($type == "professeur"){
			return $this->isRegistered2("professeur", $check, "login_prof");
		}else return null;
    }
	
	public function isRegistered2($table, $check, $field) : bool {
        $check = $this->db->select("SELECT ".$field." FROM ". $table . " WHERE " . $field . " = :check",
            array(
                ':check' => $check
            )
        );
		
        return !empty($check);
    }
	
	private function getMode($check){
		if($this->isRegistered2("etudiant", $check, "login_etu")){
			return "etudiant";
		}
		
		if($this->isRegistered2("professeur", $check, "login_prof")){
			return "professeur";
		}
	}
	
	public function getPassword($check, $type){
		if($type == "etudiant"){
			return $this->getPasswordStu($check);
		}else if($type == "professeur"){
			return $this->getPasswordProf($check);
		}else return null;
	}
	
	private function getPasswordProf($check) : string {
        $check = $this->db->select("SELECT pass_prof FROM professeur WHERE login_prof = :check",
            array(
                ':check' => $check
            )
        );
        return $check[0]->pass_prof;
    }
	
	private function getPasswordStu($check) : string {
                $check = $this->db->select("SELECT pass_etu FROM etudiant WHERE login_etu = :check",
            array(
                ':check' => $check
            )
        );
        return $check[0]->pass_etu;
    }
	
	public function getDatas($mode, $check) : object {
		$field = ($mode == "etudiant") ? "login_etu" : "login_prof";
        $check = $this->db->select("SELECT * FROM " .  $mode  ." WHERE " . $field . " = :check",
            array(
                ':check' => $check
            )
        );
        return $check[0];
    }
}