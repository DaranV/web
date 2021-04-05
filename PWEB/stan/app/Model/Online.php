<?php
namespace App\Model;

use Core\Model;

class Online extends Model
{

    protected $maxtime = (60 * 5);

    public function __construct()
    {
        parent::__construct();
    }

    public function refresh() {
		return $this->db->update("etudiant", [
			"online_time" => 0,
			"bConnect" => 0
		], [
			"online_time" => (time() - $this->maxtime)
        ], "<");
    }
	
	public function update($id){
        return $this->db->update("etudiant", [
			"online_time" => time(),
			"bConnect" => 1
		], array(
            "id_etu" => $id
        ));
    }
	
	public function logout($id){
        return $this->db->update("etudiant", [
			"online_time" => 0,
			"bConnect" => 0
		], array(
            "id_etu" => $id
        ));
    }
}