<?php
namespace Core;

use Web\Request;
use Web\Response;
use Web\Url;
use Stan\Stan;
use Core\Middleware;
use Usage\Arr;
use Session\Session;

class Router
{
    /**
     * Return an instance of Router
     *
     * @var Router
     */
    private static $instance;

    /**
     * Saved Routes
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Default Route
     *
     * @var string
     */
    private $defaultRoute = null;

	private $globalMiddlewares = [];

    /**
     * @var string
     */
    protected $matchedRoute = null;

    /**
     * @var string
     */
    private $errorCallback = '';

    /**
     * Router constructor.
     */

    public function __construct()
    {
        self::$instance =& $this;
    }

    /**
     * Get Instance of Router
     *
     * @return Router
     */

    public static function &getInstance() : Router
    {
		if(!self::$instance){
			self::$instance = new Router();
		}
		
		return self::$instance;
    }

	public static function globalMiddlewares($m){
		$router = self::getInstance();
		
		if(is_array($m)){
			foreach($m as $middleware){
				if(is_string($middleware)){
					$tmp = APPDIR . "Middleware" . DS . $middleware . ".php";
				
					if(!file_exists($tmp)){
						throw new \Exception("The middleware " . $middleware . " doesn't exist ! (" . $tmp . ")");
					}
					
					$class = "App\Middleware\\$middleware";
					$inst = new $class();
					
					if(!($inst instanceof Middleware)){
						throw new \Exception("The middleware " . $middleware . " is not an instance of Core\Middleware");
					}
				}else if(!($middleware instanceof Middleware)){
					throw new \Exception("The middleware " . get_class($middleware) . " is not an instance of Core\Middleware");
				}
			}
			
			$router->globalMiddlewares = $m;
			
		}else{
			if(is_string($m)){
				$tmp = APPDIR . "Middleware" . DS . $m . ".php";
			
				if(!file_exists($tmp)){
					throw new \Exception("The middleware " . $m . " doesn't exist ! (" . $tmp . ")");
				}
				
				$class = "App\Middleware\\$m";
				$inst = new $class();
				
				if(!($inst instanceof Middleware)){
					throw new \Exception("The middleware " . $m . " is not an instance of Core\Middleware");
				}
			}else if(!($m instanceof Middleware)){
				throw new \Exception("The middleware " . get_class($m) . " is not an instance of Core\Middleware");
			}
			$router->globalMiddlewares[] = $m;
		}
	}

    /**
     * Call Static Method
     *
     * @param string $method
     * @param array $params
     */
    public static function __callStatic(string $method, array $params)
    {
        $router = self::getInstance();
        $stan = Stan::getInstance();

        $override = (@!is_null($params[1]["override"])) ? $params[1]["override"] : false;
        $params[0] = ($params[0]!= "") ? $params[0] = "/" . $params[0] : "";
        $params[0] = (@!is_null($params[1]["domain"])) ? $params[1]["domain"] . $params[0] : $stan->configs->config->get("SITEURL") . $params[0];

        if(strpos($params[0], '.*')){
            $params[0] = str_replace("*", $stan->configs->config->get("SITEURL"), $params[0]);
        }

		$middlewares = (@!is_null($params[1]["middlewares"])) ? $params[1]["middlewares"] : [];
		$name = (@!is_null($params[1]["name"])) ? $params[1]["name"] : "";

		foreach($middlewares as $middleware){
			if(is_string($middleware)){
				$tmp = APPDIR . "Middleware" . DS . $middleware . ".php";
			
				if(!file_exists($tmp)){
					throw new \Exception("The middleware " . $middleware . " doesn't exist ! (" . $tmp . ")");
				}
				
				$class = "App\Middleware\\$middleware";
				$inst = new $class();
				
				if(!($inst instanceof Middleware)){
					throw new \Exception("The middleware " . $middleware . " is not an instance of Core\Middleware");
				}
			}else if(!($middleware instanceof Middleware)){
				throw new \Exception("The middleware " . get_class($middleware) . " is not an instance of Core\Middleware");
			}
		}

        $params[1] = "App\Controller\\" . $params[1]["controller"] . "@" . $params[1]["method"];
        
        $path = explode("@", $params[1]);
        $path = str_replace("App/", "", str_replace("\\", "/", $path[0]));
		
        if(file_exists(APPDIR . $path . ".php")){
            $router->addRoute($method, $params[0], $params[1], $override, $name, $middlewares);
        }
    }

    /**
     * Set the Error Callback
     *
     * @param string $callback
     */
    public static function error(string $callback)
    {
        $router = self::getInstance();
        $router->callback($callback);
    }

    /**
     * Add Route
     *
     * @param string $method
     * @param string $route
     * @param string|null $callback
     */
    public static function match(string $method, string $route, string $callback = null)
    {
        $router =& self::getInstance();
		$router->addRoute($method, $route, $callback);
    }

    /**
     * Return Saved Routes
     *
     * @return array
     */

    public function routes() : array
    {
        return $this->routes;
    }

    /**
     * Get Error Calback if defined, null if not
     *
     * @param string|null $callback
     * @return null|string
     */
    public function callback(string $callback = null)
    {
        if (is_null($callback)) {
            return $this->errorCallback;
        } //is_null($callback)
        
        $this->errorCallback = $callback;
        
        return null;
    }

    /**
     * Add Route
     *
     * @param string $method
     * @param string $route
     * @param string|null $callback
     * @param bool $override
     */
    public function addRoute(string $method, string $route, string $callback = null, bool $override = false, string $name, array $middlewares)
    {
        $methods = array_map('strtoupper', is_array($method) ? $method : array(
            $method
        ));
        $pattern = ltrim($route, '/');
        
		foreach ($this->routes as $r){
			if($r->name() == $name){
				throw new \Exception("A route is already using this name : " . $name);
				return;
			}
		}
		
		$route = new Route($methods, $pattern, $callback, $name, array_merge($middlewares, $this->globalMiddlewares));
		
        if($override){
            $custom = array();
            foreach ($this->routes as $r){
                if($r->pattern() != $route->pattern()){
                    array_push($custom, $r);
                }
            }
            $this->routes = $custom;
        }
        array_push($this->routes, $route);
    }

    /**
     * Get Matched Route
     *
     * @return string
     */

    public function matchedRoute() : string
    {
        return $this->matchedRoute;
    }

    /**
     * Invoke a Controller
     *
     * @param string $className
     * @param string $method
     * @param array $params
     * @return bool
     */

    public function invokeController(string $className, string $method, array $params) : bool
    {
		$controller = new $className();
		
        if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($controller)))) {
            return false;
        } //!in_array(strtolower($method), array_map('strtolower', get_class_methods($controller)))
		
        call_user_func_array(array(
            $controller,
            $method
        ), $params);
        return true;
    }

    /**
     * Invoke a Method
     *
     * @param string $callback
     * @param array $params
     * @return bool
     */

    public function invokeObject(string $callback, $params = array(), array $middlewares = array(), $route) : bool
    {
		foreach($middlewares as $middleware){
			if(is_string($middleware)){
				$class = "App\Middleware\\$middleware";
				$middleware = new $class();
			}
			
			$continue = call_user_func_array(array(
				$middleware,
				"handle"
			), []);
			
			if(!$continue) {
				Response::sendHeaders();
				return $this->invokeObject($this->callback(), [], [],  null);
			}
		}
		
        if (is_object($callback)) {
            call_user_func_array($callback, $params);
            return true;
        } //is_object($callback)
        $segments = explode('@', $callback);
        
        $controller = $segments[0];
        $method     = $segments[1];
		
		$tmp = explode(":", $method);
		if(count($tmp) > 0){
			$method = $tmp[0];
			array_shift($tmp);
			$params = array_merge($params, $tmp);
		}
		
		Response::sendHeaders();
		
        if ((($method[0] !== '_')) && class_exists($controller) && method_exists($controller, $method)) {
            return $this->invokeController($controller, $method, $params);
        } else{
            $this->invokeObject($this->callback(), ["error" => "The method '" . $method . "' doesn't exist in the controller '" . substr($controller, strrpos($controller, '\\') + 1) . "'!"], [], null);
        }
        return false;
    }

    /**
     * Dispatch Method
     *
     * @return bool
     */

    public function dispatch() : bool
    {
        $uri = Url::detectUri(true);
        if(substr($uri, -1) == "/"){
            $uri = substr($uri, 0, -1);
        }
        if (Request::isGet() && $this->dispatchFile($uri)) {
            return true;
        } //Request::isGet() && $this->dispatchFile($uri)
        $method = Request::getMethod();

        if ($this->defaultRoute !== null) {
            array_push($this->routes, $this->defaultRoute);
        } //$this->defaultRoute !== null
		
        foreach ($this->routes as $route) {
            if ($route->match($uri, $method)) {
                $this->matchedRoute = $route;

                $callback = $route->callback();

                if ($callback !== null) {
                    return $this->invokeObject($callback, $route->params(), $route->middlewares(), $route);
                } //$callback !== null

                return true;
            } //$route->match($uri, $method)
        } //$this->routes as $route

        $this->invokeObject($this->callback(), [], [], null);

        return false;
    }


    /**
     * Dispatch File Method
     *
     * @param string $uri
     * @return bool
     */

    protected function dispatchFile(string $uri) : bool
    {
        $filePath = '';
        $stan = Stan::getInstance();
        
        if (preg_match('#^assets/(.*)$#i', $uri, $matches)) {
            $filePath = ROOTDIR . 'assets' . DS . $matches[1];
        } //preg_match('#^assets/(.*)$#i', $uri, $matches)
        
        if (!empty($filePath)) {
            Response::serveFile($filePath);
            
            return true;
        } //!empty($filePath)
        
        return false;
    }
    
}
