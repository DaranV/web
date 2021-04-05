<?php
namespace Security;

use Session\Session;
use Web\Request;

class Csrf
{

    /**
     * Make a Token
     *
     * @param string $name
     * @return string
     */

    public static function makeToken(string $name) : string
    {
        $max_time    = 60 * 60 * 24;
        $csrf_token  = Session::read($name);
        $stored_time = Session::read($name . '_time');
        
        if ($max_time + $stored_time <= time() || empty($csrf_token)) {
            Session::write($name, md5(uniqid(rand(), true)));
            Session::write($name . '_time', time());
        } //$max_time + $stored_time <= time() || empty($csrf_token)
        
        return Session::read($name);
    }

    /**
     * Is a valid Token
     *
     * @param string $name
     * @return bool
     */

    public static function isTokenValid(string $name) : bool
    {
        return Request::post($name) === Session::read($name);
    }
}
