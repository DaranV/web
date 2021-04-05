<?php
namespace App\Controller;

use Core\Controller;

class ErrorController extends Controller{

    private $error = null;

   
    public function __construct($error = null)
    {
        parent::__construct();
        $this->error = $error;
    }

    public function index($error = null){
		$this->view->render(["folder" => "error", "file" => "error"]);
    }
}


