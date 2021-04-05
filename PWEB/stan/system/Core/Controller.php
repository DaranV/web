<?php
namespace Core;

use Web\Url;
use Stan\Stan;
use Core\View;
use Session\Session;
use Web\Request;
use Usage\Flash;

abstract class Controller
{
    /**
     * Return Url Class
     *
     * @var Url
     */
    public $url;

    /**
     * Return Stan Class
     *
     * @var Stan
     */
	public $stan;

    /**
     * Return View Class
     *
     * @var View
     */
	public $view;

    /**
     * Return Session Class
     *
     * @var Session
     */
	public $session;

    /**
     * Return Flash Class
     *
     * @var Flash
     */
	public $flash;

    /**
     * Return Request Class
     *
     * @var Request
     */
	public $request;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->url = new Url();
        $this->stan = Stan::getInstance();
		$this->view = new View();
		$this->session = new Session();
        $this->flash = Flash::getInstance();
        $this->request = new Request();
        $this->request->setController(substr(get_class($this), strrpos(get_class($this), '\\') + 1));
    }

    /**
     * Load Model
     *
     * @param string|array $name
     */
	function loadModel($name){
        if(is_array($name)){
            foreach ($name as $c){
                $slug = "App\Model";
                $class = $slug . "\\$c";
                $this->$c = @new $class();
            }
        }else if(!isset($this->$name)){
			$class = "App\Model\\$name";
			$this->$name = new $class();
		}
	}

    /**
     * Display Error Page
     */
	function e404(){
	    $this->view->set(["title" => "Erreur 404"]);
	    $this->view->render(["folder" => "error", "file" => "index"]);
    }
}
