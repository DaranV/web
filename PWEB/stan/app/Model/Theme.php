<?php
namespace App\Model;

use Core\Model;
use Usage\Date;

class Theme extends Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getThemes() : array {
        $tmp = $this->db->select("SELECT *, (SELECT count(*) FROM question q WHERE q.id_theme = theme.id_theme) as nbQuestion FROM theme", array());
		return $tmp;
    }
	
    public function getTheme($id, string $column = "id_theme"){
        $result = $this->db->select("SELECT * FROM theme WHERE $column = :check", array(
            ":check" => $id
        ));
        return $result[0];
    }
	
	public function getFormatedQuestions(){
		$questions = $this->db->select("SELECT * from question", array());
		$tmp = [];
		
		foreach($questions as $question){
			$tmp[$question->id_theme]["questions"][] = $question;
			if($question->id_theme == -1) {
				$tmp[$question->id_theme]["title"] = "Non repertoriée";
			}else{
				$tmp[$question->id_theme]["title"] = $this->getTheme($question->id_theme)->titre_theme;
			}
		}
		
		return $tmp;
	}
	
    public function exist($id, string $column = "id_theme") : bool {
        $check = $this->db->select("SELECT $column FROM theme WHERE $column = :check",
            array(
                ':check' => $id
            )
        );
        return !empty($check);
    }
	
	public function del(int $id) : int {
		$questions = $this->db->select("SELECT * FROM question WHERE id_theme = :check",
            array(
                ':check' => $id
            )
        );
		
		foreach($questions as $question){
			(new Question())->update([
				"id_theme" => -1
			], $question->id_quest);
		}
		
		$check = $this->db->delete("theme", array(
			"id_theme" => $id
		));

		return $check;
    }
	
	public function add($datas) : string {
        return $this->db->insert("theme", $datas);
    }
	
	
	public function update($datas, $id) : int {
		return $this->db->update("theme", $datas, array(
			"id_theme" => $id
		));
    }
}
