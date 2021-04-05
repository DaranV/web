<?php
namespace Core;

use Stan\Stan;

class Route
{
    /**
     * Return Methods
     * @var array
     */
    private $methods = array();

    /**
     * Used Pattern
     * @var null|string
     */
    private $pattern = null;

    /**
     * Return callback
     * @var string
     */
    private $callback = null;

    /**
     * Used method
     * @var string
     */
    private $method = null;

    /**
     * Used params
     * @var array
     */
    private $params = array();

    /**
     * Used regex
     * @var string
     */
    private $regex;
	
	private $name;
	
	private $middlewares = array();
	
    /**
     * Route constructor.
     * @param array $method
     * @param string $pattern
     * @param string $callback
     */
    public function __construct(array $method, string $pattern, string $callback, string $name, array $middlewares)
    {
        $this->methods  = array_map('strtoupper', is_array($method) ? $method : array(
            $method
        ));
        $this->pattern  = !empty($pattern) ? $pattern : '/';
        $this->callback = $callback;
		$this->name = $name;
		$this->middlewares = $middlewares;
    }

    /**
     * Match Uri
     *
     * @param string $uri
     * @param string $method
     * @param bool $optionals
     * @return bool
     */
    public function match(string $uri, string $method, bool $optionals = true) : bool
    {
        if (!in_array('ANY', $this->methods) && !in_array($method, $this->methods)) {
            return false;
        } //!in_array('ANY', $this->methods) && !in_array($method, $this->methods)
        $this->method = $method;
        if ($this->pattern == $uri) {
            return true;
        } //$this->pattern == $uri
        
        if (strpos($this->pattern, ':') !== false) {
			
            $regex = str_replace(array(
                ':any',
                ':num',
                ':all',
				':slug',
				':token'
            ), array(
                '[^/]+',
                '-?[0-9]+',
                '.*',
				'[a-zA-Z0-9-]+',
				'[a-zA-Z0-9]+'
            ), $this->pattern);
        } //strpos($this->pattern, ':') !== false
        else {
            $regex = $this->pattern;
        }
        
        if ($optionals) {
            $regex = str_replace(array(
                '(/',
                ')'
            ), array(
                '(?:/',
                ')?'
            ), $regex);
        } //$optionals
        
        if (preg_match('#^' . $regex . '(?:\?.*)?$#i', $uri, $matches)) {
            array_shift($matches);
            $this->params = $matches;
            $this->regex  = $regex;
            
            return true;
        } //preg_match('#^' . $regex . '(?:\?.*)?$#i', $uri, $matches)
        
        return false;
    }

    /**
     * Get Mathods
     *
     * @return array
     */

    public function methods() : array
    {
        return $this->methods;
    }

    /**
     * Get Pattern
     *
     * @return string
     */

    public function pattern() : string
    {
        return $this->pattern;
    }

    /**
     * Get Callback
     *
     * @return string
     */

    public function callback() : string
    {
        return $this->callback;
    }

    /**
     * Get Method
     *
     * @return string
     */

    public function method() : string
    {
        return $this->method;
    }

    /**
     * Get Params
     *
     * @return array
     */

    public function params() : array
    {
        return $this->params;
    }

    /**
     * Get Regex
     *
     * @return string
     */

    public function regex()
    {
        return $this->regex;
    }
	
	public function name() : string
    {
        return $this->name;
    }
	
	public function middlewares() : array
    {
        return $this->middlewares;
    }
}
