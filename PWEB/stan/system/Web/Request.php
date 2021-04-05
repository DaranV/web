<?php
namespace Web;

use Security\Security;

/**
 * Class Request
 * @package Web
 */

class Request
{
    /**
     * Return Security Class
     * @var Security
     */
    private static $security;

    /**
     * Return Controller Name
     * @var string
     */
    private static $controller;

    /**
     * Init Method
     */

    public static function init(){
        self::$security = new Security();
    }

    /**
     * Get Method
     *
     * @return string
     */

    public static function getMethod() : string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))  $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        elseif (isset($_REQUEST['_method'])) $method = $_REQUEST['_method'];

        return strtoupper($method);
    }

    /**
     * Get Post Value(s)
     *
     * @param string $key
     * @return mixed|string|integer|array|null
     */

    public static function post(string $key)
    {
        self::init();
        return array_key_exists($key, $_POST) ? self::$security->xss_clean($_POST[$key]) : null;
    }

    /**
     * Get Post File(s)
     *
     * @param string $key
     * @return mixed|array|null
     */

    public static function files(string $key)
    {
        return array_key_exists($key, $_FILES) ? $_FILES[$key] : null;
    }

    /**
     * Get Query Value(s)
     *
     * @param string $key
     * @return mixed|string|integer|array|null
     */

    public static function query(string $key)
    {
        return self::get($key);
    }

    /**
     * Get Value(s)
     *
     * @param string $key
     * @return mixed|string|integer|array|null
     */
    public static function get(string $key)
    {
        self::init();
        return array_key_exists($key, $_GET) ? self::$security->xss_clean($_GET[$key]) : null;
    }

    /**
     * Is Ajax Request
     *
     * @return bool
     */

    public static function isAjax() : bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        return false;
    }

    /**
     * Is Post Request
     *
     * @return bool
     */

    public static function isPost() : bool
    {
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    /**
     * Is Delete Request
     *
     * @return bool
     */

    public static function isDelete() : bool
    {
        return $_SERVER["REQUEST_METHOD"] === "DELETE";
    }

    /**
     * Is Put Request
     *
     * @return bool
     */

    public static function isPut() : bool
    {
        return $_SERVER["REQUEST_METHOD"] === "PUT";
    }

    /**
     * Is Get Request
     *
     * @return bool
     */

    public static function isGet() : bool
    {
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    /**
     * Get Header
     *
     * @param string $header
     * @return array|bool|mixed|null|string
     */

    public static function getHeader(string $header)
    {
        self::init();
        return array_key_exists("HTTP_" . strtoupper($header), $_SERVER) ? self::$security->xss_clean($_SERVER["HTTP_" . strtoupper($header)]) : null;
    }

    /**
     * Get Controller Name
     *
     * @return string
     */

    public static function getController() : string
    {
        return self::$controller;
    }

    /**
     * Set Controller Name
     *
     * @param string $controller
     */

    public static function setController(string $controller)
    {
        self::$controller = $controller;
    }
}
