<?php
namespace Session;

class Session
{
    /**
     * Is Session Started
     * @var bool
     */
    private static $sessionStarted = false;

    /**
     * Init Session Method
     */
    public static function init()
    {
        if (self::$sessionStarted == false) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    /**
     * Write Data(s)
     *
     * @param string|array $key
     * @param bool|string|array $value
     * @param bool|string|array $secondkey
     */

    public static function write($key, $value = false, $secondkey = false)
    {
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                if(!$secondkey){
                    $_SESSION[$name] = $value;
                }else{
                    $_SESSION[$name][$secondkey] = $value;
                }
            }
        } 
        else {
            if(!$secondkey) {
                $_SESSION[$key] = $value;
            }else{
                $_SESSION[$key][$secondkey] = $value;
            }
        }
    }

    /**
     * Add Data(s)
     *
     * @param string|array $key
     * @param bool|string|array $value
     * @param bool|string|array $secondkey
     */
    public static function add($key, $value = false, $secondkey = false)
    {
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                if(!$secondkey){
                    $_SESSION[$name][] = $value;
                }else{
                    $_SESSION[$name][$secondkey][] = $value;
                }
            }
        }
        else {
            if(!$secondkey) {
                $_SESSION[$key][] = $value;
            }else{
                $_SESSION[$key][$secondkey][] = $value;
            }
        }
    }

    /**
     * Override Object
     *
     * @param string|array $key
     * @param bool|string|array $value
     * @param bool|string|array $secondkey
     */

    public static function overrideObject($key, $value = false, $secondkey = false){
        if (is_array($key) && $value === false) {
            foreach ($key as $name => $value) {
                if(!$secondkey){
                    $_SESSION[$name] = $value;
                }else{
                    $_SESSION[$name]->$secondkey = $value;
                }
            }
        }
        else {
            if(!$secondkey) {
                $_SESSION[$key] = $value;
            }else{
                $_SESSION[$key]->$secondkey = $value;
            }
        }
    }

    /**
     * Pull Data
     *
     * @param string $key
     * @return string|null
     */

    public static function pull(string $key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return null;
    }

    /**
     * Read Data
     *
     * @param string $key
     * @param bool|string|array $secondkey
     * @return string|array|null
     */
    public static function read(string $key, $secondkey = false)
    {
        if ($secondkey) {
            if (isset($_SESSION[$key][$secondkey])) {
                return $_SESSION[$key][$secondkey];
            } 
        }
        else {
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            } 
        }
        return null;
    }

    /**
     * Get Session ID
     *
     * @return string
     */
    public static function id() : string
    {
        return session_id();
    }

    /**
     * Get New Session ID
     *
     * @return string
     */
    public static function regenerate() : string
    {
        session_regenerate_id(true);
        return session_id();
    }

    /**
     * Get All Session Data(s)
     *
     * @return array
     */
    public static function display() : array
    {
        return $_SESSION;
    }

    /**
     * Destroy Session or Delete Values
     *
     * @param string|array $key
     * @param bool|string $secondkey
     */

    public static function destroy($key = '', $secondkey = false)
    {
        if (self::$sessionStarted) {
            if ($key == '') {
                session_unset();
                session_destroy();
            } else{
                if($secondkey){
                    unset($_SESSION[$key][$secondkey]);
                }else{
                    if(is_array($key)){
                        foreach ($key as $value) {
                            unset($_SESSION[$value]);
                        }
                    }else{
                        unset($_SESSION[$key]);
                    }
                }
            }
        }
    }

    /**
     * Read User Data
     *
     * @param string $key
     * @return array|null|string
     */
    public static function readUser(string $key){
        return self::read("user", $key);
    }

    /**
     * Write User Data(s)
     *
     * @param string $key
     * @param mixed|string|array|integer $value
     */
    public static function writeUser(string $key, $value){
        self::write("user", $value, $key);
    }
}
