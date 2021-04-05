<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Group extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getGroups() : array {
        return $this->db->select("SELECT *, (SELECT count(*) FROM grpetudiants WHERE groupe.id_grpe = grpetudiants.id_grpe) as nbStudent FROM groupe", array());
    }
	
    public function getGroup($id, string $column = "id_grpe"){
        $result = $this->db->select("SELECT * FROM groupe WHERE $column = :check", array(
            ":check" => $id
        ));
        return $result[0];
    }

	public function getStudentsInGroup($id){
		$result = $this->db->select("SELECT * FROM grpetudiants gp, etudiant e WHERE gp.id_etu = e.id_etu AND gp.id_grpe = :check", array(
            ":check" => $id
        ));
        return $result;
	}
	
	public function getStudentsConnectedInGroup($id){
		$result = $this->db->select("SELECT * FROM grpetudiants gp, etudiant e WHERE gp.id_etu = e.id_etu AND e.bConnect = 1 AND gp.id_grpe = :check", array(
            ":check" => $id
        ));
        return $result;
	}
	
	public function getStudentsNotInGroup($id){
		$result = $this->db->select("SELECT * FROM etudiant e WHERE NOT EXISTS (SELECT id_etu FROM grpetudiants gp WHERE gp.id_etu = e.id_etu AND gp.id_grpe = :check)", array(
            ":check" => $id
        ));
        return $result;
	}
	
	public function studentHasGroup($id){
        return ($this->getGroupOf($id) != null) ? true : false;
	}

	public function getGroupOf($id){
		 $result = $this->db->select("SELECT * FROM grpetudiants WHERE id_etu = $id", array(
            ":check" => $id
        ));
        return ($result != null) ? $result[0] : null;
	}

    public function exist($id, string $column = "id_grpe") : bool {
        $check = $this->db->select("SELECT $column FROM groupe WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$students = $this->getStudentsInGroup($id);
		
		foreach($students as $student){
			$this->removeGroupOf($student->id_etu);
		}
		
		$check = $this->db->delete("groupe", array(
			"id_grpe" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("groupe", $datas);
    }
	
	public function addToGroup($datas) : string {
        return $this->db->insert("groupe", $datas);
    }
	
	public function update($datas, $id) : int {
		return $this->db->update("groupe", $datas, array(
			"id_grpe" => $id
		));
    }
	
	public function updateGroupOf($newGroupId, $lastGroupId, $studentId) : int {
		return $this->db->update("grpetudiants", [
			"id_grpe" => $newGroupId
		], array(
			"id_grpe" => $lastGroupId,
			"id_etu" => $studentId,
		));
    }
	
	public function insetGroupOf($newGroupId, $studentId) : int {
		return $this->db->insert("grpetudiants", array(
			"id_grpe" => $newGroupId,
			"id_etu" => $studentId,
		));
    }
	
	public function removeGroupOf($studentId){
		$check = $this->db->delete("grpetudiants", array(
			"id_etu" => $studentId
		));

		return $check;
	}
}
