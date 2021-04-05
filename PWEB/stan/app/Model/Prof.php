<?php
namespace App\Model;

use Core\Model;

class Prof extends Model {

    public function __construct()
    {
        parent::__construct();
    }

	public function update($datas, $id) : int {
		return $this->db->update("professeur", $datas, array(
			"id_prof" => $id
		));
    }
	
	public function getProf($id, string $column = "id_prof"){
        $result = $this->db->select("SELECT * FROM professeur WHERE $column = :check", array(
            ":check" => $id
        ));
        return $result[0];
    }

    public function exist($id, string $column = "id_prof") : bool {
        $check = $this->db->select("SELECT $column FROM professeur WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }

}