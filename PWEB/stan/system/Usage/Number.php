<?php
namespace Usage;

class Number
{

    /**
     * Get the Percentage
     *
     * @param int $val1
     * @param int $val2
     * @return string
     */
    public static function percentage(int $val1, int $val2) : string
    {
        if ($val1 > 0 && $val2 > 0) {
            $division = $val1 / $val2;
            $res      = $division * 100;
            //return round($res);
            return $res;
        } //$val1 > 0 && $val2 > 0
        else {
            return '0%';
        }
    }

    /**
     * Create a Key
     *
     * @param int $length
     * @param bool $onlyAlphaNumeric
     * @return string
     */
    public static function createkey(int $length = 32, bool $onlyAlphaNumeric = false) : string
    {
        $chars = (!$onlyAlphaNumeric) ? "!@#$%^&*()_+-=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890" : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $key   = "";
		
		srand(time());

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars{rand(0, strlen($chars) - 1)};
        } //$i = 0; $i < $length; $i++

        return $key;
    }
}
