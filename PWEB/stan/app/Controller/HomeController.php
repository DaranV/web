<?php
namespace App\Controller;

use Core\Controller;
use Web\Url;

class HomeController extends Controller{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
		if($this->session->readUser("type") == "professeur"){
			$c = new TeacherController();
			$c->index();
		}else{
			$c = new StudentController();
			$c->index();
		}
		exit();
    }
}