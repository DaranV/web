<?php
namespace Web;

use Session\Session;
use Web\Inflector;
use Stan\Stan;
use Core\Router;

class Url
{

    /**
     * Redirect
     *
     * @param string|null $url
     * @param bool $fullpath
     * @param int $code
     */
    public static function redirect(string $url = null, bool $fullpath = false, int $code = 200)
    {
        $stan = Stan::getInstance();
        $url     = ($fullpath === false) ? $stan->configs->config->get("SITEURL") . "/" .$url : $url;
        
		self::setResponseCode($code);
        header('Location: //' . $url);
		
        exit;
    }
	
	public static function setResponseCode($code){
		http_response_code($code);
	}

    /**
     * Get Main Url
     *
     * @return string
     */

    public static function getMainUrl() : string {
        return BASE_URL;
        exit;
    }

    /**
     * Redirect to Main Page
     */

    public static function mainPage()
    {
        header('Location: /');
        exit;
    }

    /**
     * Detect Uri
     *
     * @param bool $domain
     * @return string
     */

    public static function detectUri(bool $domain = false) : string
    {
		$requestUri = (!$domain) ? $_SERVER['REQUEST_URI'] : $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        $pathName = dirname($scriptName);
        
        if (strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        } //strpos($requestUri, $scriptName) === 0
        else if (strpos($requestUri, $pathName) === 0) {
            $requestUri = substr($requestUri, strlen($pathName));
        } //strpos($requestUri, $pathName) === 0
        
        $uri = parse_url(ltrim($requestUri, '/'), PHP_URL_PATH);
        
        if (!empty($uri)) {
            return str_replace(array(
                '//',
                '../'
            ), '/', $uri);
        } //!empty($uri)
        
        return '/';
    }

    /**
     * Get a File in a Template Path
     *
     * @param string $file
     * @return string
     */

    public static function templatePath(string $file = "") : string
    {
        $stan = Stan::getInstance();
        return '/assets/' . $file;
    }

    /**
     * Get a File in a Relative Template Path
     *
     * @param string $custom
     * @param string $folder
     * @return string
     */

    public static function relativeTemplatePath(string $custom = "") : string
    {
        $stan = Stan::getInstance();
        return '/assets/' . $custom ;
    }

    /**
     * Redirect To Previous Visited Page
     */
    public static function previous()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Get Segmented Uri
     *
     * @return array
     */

    public static function segments() : array
    {
        return explode('/', $_SERVER['REQUEST_URI']);
    }

    /**
     * Get Segment
     *
     * @param array $segments
     * @param string $id
     * @return mixed
     */

    public static function getSegment(array $segments, string $id)
    {
        if (array_key_exists($id, $segments)) {
            return $segments[$id];
        } //array_key_exists($id, $segments)
    }

    /**
     * Get Last Segment
     *
     * @param array $segments
     * @return mixed
     */

    public static function lastSegment(array $segments)
    {
        return end($segments);
    }

    /**
     * Get First Segment
     *
     * @param array $segments
     * @return mixed
     */

    public static function firstSegment(array $segments)
    {
        return $segments[0];
    }

    /**
     * Get Url from route name
     *
     * @param string $url
     * @param bool $useMainDomain
     * @return string
     */

    public static function getUrl(string $url, bool $useMainDomain = true) : string
    {
        $stan = Stan::getInstance();
        return (($useMainDomain) ? "//" .$stan->configs->config->get("SITEURL") : "") . BASE_URL . trim($url, '/');
    }
	
	public static function connect(string $routeName, ...$params){
		foreach(Router::getInstance()->routes() as $route){
			if($route->name() == $routeName){
				if(strpos($route->pattern(), ':') !== false && count($params) == 0){
					return "invalid_route_params";
				}else{
					if(substr_count($route->pattern(), ":") == count($params)){
						$i = 0;
						
						$testUrl = str_replace("(:any)", "{}", $route->pattern());
						$testUrl = str_replace("(:num)", "{}", $testUrl);
						$testUrl = str_replace("(:all)", "{}", $testUrl);
						$testUrl = str_replace("(:any)", "{}", $testUrl);
						$testUrl = str_replace("(:let)", "{}", $testUrl);
						$testUrl = str_replace("(:slug)", "{}", $testUrl);
						$testUrl = str_replace("(:token)", "{}", $testUrl);
						$testUrl = str_replace("(:lang)", "{}", $testUrl);
						
						$testUrl = preg_replace_callback("|{}|", function() use (&$i, $params) {
							return $params[$i++];
						}, $testUrl);
						
						return ($route->match($testUrl, $route->methods()[0])) ? $testUrl : "invalid_route_params";
					}else{
						return "invalid_route_params";
					}
				}
				
				return $route->pattern();
			}
		}
		
		return "invalid_route";
	}
}
