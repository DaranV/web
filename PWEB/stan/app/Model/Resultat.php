<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Resultat extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getResultats() : array {
        $tmp = $this->db->select("SELECT * FROM resultat", array());
		return $tmp;
    }
	
    public function getResultat($id, string $column = "id_res"){
        $result = $this->db->select("SELECT * FROM resultat WHERE $column = :check", array(
            ":check" => $id
        ));
        return $result[0];
    }

    public function exist($id, string $column = "id_res") : bool {
        $check = $this->db->select("SELECT $column FROM resultat WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function studentHasAnswered($test_id, $etu_id, $quest_id) : bool {
        $check = $this->db->select("SELECT * FROM resultat WHERE id_test = :t AND id_quest = :q AND id_etu = :e",
            array(
                ':t' => $test_id,
                ':q' => $quest_id,
                ':e' => $etu_id,
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$check = $this->db->delete("resultat", array(
			"id_res" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("resultat", $datas);
    }
	
	
	public function update($datas, $id) : int {
		return $this->db->update("resultat", $datas, array(
			"id_res" => $id
		));
    }
}
