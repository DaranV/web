<?php
namespace Core;

use App\Controller\ErrorController;
use Stan\Stan;
use Session\Session;
use Web\Url;
use Usage\Arr;
use Usage\Flash;

class View
{

    private static $headers = array();
    private static $data = array();
    private static $autoRender = true;
	private static $layout = ""; 

    public static function render(array $array)
    {
        $stan = Stan::getInstance();
        
        $path = APPDIR . "View" . DS . $array["folder"] . DS . $array["file"] . ".tpl";
	
        if(!file_exists($path)) self::displayErrorPage($array["file"], $array["folder"]);

        if(self::$autoRender){
            self::renderTemplate("header");
            self::toRender($path);
            self::renderTemplate("footer");
        }else{
            self::toRender($path);
        }
    }

    /**
     * Render file
     *
     * @param string $path
     */

    private static function toRender(string $path){
        self::sendHeaders();
        $stan = Stan::getInstance();
        $flash = Flash::getInstance();
        $xmlfile = "";
        $data = self::$data;
        if($data != null){
            foreach ($data as $name => $value) {
                ${$name} = $value;
            }
        }

        if(file_exists($path)){
            require $path;
        }else{
            die("File " . $path  . " not found !");
        }
    }

    /**
     * Set or Add Data(s)
     *
     * @param array $data
     * @param bool $reset
     */

    public static function set(array $data, bool $reset = false){
        if($reset){
            self::$data = $data;
        }else{
            self::$data = Arr::merge(self::$data, $data);
        }
    }

    /**
     * Set or Add Css File(s)
     *
     * @param array|string $data
     */

    public static function css($data){
        if(is_array($data)){
            self::$data["css"] = Arr::merge((!array_key_exists("css", self::$data)) ? array() : self::$data["css"], $data);
        }else{
            self::$data["css"][] = $data;
        }
    }

    /**
     * Set or Add Js File(s)
     *
     * @param array|string $data
     */

    public static function js($data){
        if(is_array($data)){
            self::$data["js"] = Arr::merge((!array_key_exists("js", self::$data)) ? array() : self::$data["js"], $data);
        }else{
            self::$data["js"][] = $data;
        }
    }

    /**
     * Set AutoRender State
     *
     * @param bool $state
     */

    public static function autoRender(bool $state){
        self::$autoRender = $state;
    }
	
	public static function setLayout(string $layout){
        self::$layout = $layout;
    }

    /**
     * Render a Template File
     *
     * @param string $path
     * @param string $custom
     */

    public static function renderTemplate(string $path)
    {
        self::sendHeaders();
        $data = self::$data;
        foreach ($data as $name => $value) {
            ${$name} = $value;
        } //$data as $name => $value
		$path = (self::$layout != "") ? self::$layout . "/" . $path : $path;
        $path = APPDIR . "View/layout/$path.tpl";
        if(file_exists($path)){
            require $path;
        }
    }

    /**
     * Add an Header
     *
     * @param string $header
     */

    public function addHeader(string $header)
    {
        self::$headers[] = $header;
    }

    /**
     * Add Headers
     *
     * @param array $headers
     */
    public function addHeaders(array $headers = array())
    {
        self::$headers = Arr::merge(self::$headers, $headers);
    }

    /**
     * Send Headers Method
     */
    public static function sendHeaders()
    {
        if (!headers_sent()) {
            foreach (self::$headers as $header) {
               header($header, true);
            } //self::$headers as $header
        } //!headers_sent()
    }

    /**
     * Display The Error Page
     *
     * @param string $file
     * @param string $folder
     */

    private static function displayErrorPage(string $file, string $folder){
        $router = Router::getInstance();
        $router->invokeObject("App\Controller\ErrorController@index", ["error" => "La vue '" . $file . "' n'existe pas dans le dossier '" . $folder . "'!"], [], null);
        exit();
    }
}
