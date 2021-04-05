<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Student extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getStudents() : array {
        $tmp = $this->db->select("SELECT * FROM etudiant", array());
		foreach($tmp as $u){
			$u->group = (new Group())->getGroupOf($u->id_etu);
			if($u->group) 
				$u->group = (new Group())->getGroup($u->group->id_grpe);
		}
		return $tmp;
    }

    public function getStudent($id, string $column = "id_etu"){
        $result = $this->db->select("SELECT * FROM etudiant WHERE $column = :check", array(
            ":check" => $id
        ));
        return $result[0];
    }

    public function exist($id, string $column = "id_etu") : bool {
        $check = $this->db->select("SELECT $column FROM etudiant WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$check = $this->db->delete("etudiant", array(
			"id_etu" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("etudiant", $datas);
    }
	
	
	public function update($datas, $id) : int {
		return $this->db->update("etudiant", $datas, array(
			"id_etu" => $id
		));
    }
}
