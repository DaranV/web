<?php
namespace Usage;

use DateTime;

class Validate
{

    /**
     * Is a Valid Email
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email) : bool
    {
        if (function_exists('filter_var')) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        } //function_exists('filter_var')
        else {
            return preg_match('/.+?@.+?\.[a-z0-9]+/i', $email);
        }
    }

    /**
     * Is a Valid Url
     *
     * @param string $url
     * @return bool
     */
    public static function validateUrl(string $url) : bool
    {
        if (function_exists('filter_var')) {
            return filter_var($url, FILTER_VALIDATE_URL);
        } //function_exists('filter_var')
        else {
            return preg_match('#(https?)://.+?#', $url);
        }
    }

    /**
     * Is an Alpha-Numeric String
     *
     * @param string $str
     * @return bool
     */
	public static function isAlphaNumeric(string $str) : bool {
		$allowed = array(".", "-", "_");
		if (ctype_alnum(str_replace($allowed, '', $str ))) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * Is a Valid ZipCode
     *
     * @param string $country_code
     * @param string $zip_postal
     * @return bool
     */
	public static function validateZipCode(string $country_code, string $zip_postal) : bool {
		$datas = array(
			"US"=>"^\d{5}([\-]?\d{4})?$",
			"UK"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
			"DE"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
			"CA"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
			"FR"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
			"IT"=>"^(V-|I-)?[0-9]{5}$",
			"AU"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
			"NL"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
			"ES"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
			"DK"=>"^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
			"SE"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
			"BE"=>"^[1-9]{1}[0-9]{3}$"
		);
		if (!preg_match("/".$datas[$country_code]."/i",$zip_postal)){
			return false;
		} else {
			return true;
		}
	}

    /**
     * Is a Valid CountryCode
     *
     * @param string $country_code
     * @return bool
     */
	public static function validateCountryCode(string $country_code) : bool{
		$datas = array("US", "UK", "DE", "CA", "FR", "IT", "AU", "NL", "ES", "DK", "SE", "BE");
		if (in_array($country_code, $datas)) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * Is a valid Timestamp
     *
     * @param int $timestamp
     * @return bool
     */
	public static function validateTimestamp(int $timestamp) : bool{
		if(strtotime(date('d-m-Y H:i:s',$timestamp)) === (int)$timestamp) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * Is a valid Date
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate(string $date, string $format = 'Y-m-d H:i:s') : bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Is a valid Ip
     *
     * @param string $ip
     * @return bool
     */
    public static function validateIp(string $ip) : bool {
        if (function_exists('filter_var')) {
            return filter_var($ip, FILTER_VALIDATE_IP);
        } else {
            if(preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip)){
                $parts = explode(".", $ip);
                foreach ($parts as $part){
                    if(intval($part) > 255 || intval($part) < 0){
                        return false;
                    }
                }
                return true;
            }else{
                return false;
            }
        }
    }
}
?>