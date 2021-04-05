<?php
namespace Security;

use Stan\Stan;

class Password {

    /**
     * Make a hashed Password
     *
     * @param string $password
     * @param bool|string $algo
     * @return string
     */

	public static function make(string $password, $algo = PASSWORD_DEFAULT) : string {
	    return password_hash($password, $algo);
    }

    /**
     * Verify a couple of password
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */

    public static function verify(string $password, string $hash) : bool{
        return password_verify($password, $hash);
    }
}
