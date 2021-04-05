<?php
namespace Usage;

use Usage\Curl;

class Geolocation
{

    /**
     * User Ip
     * @var string
     */
    private $ip;

    /**
     * User City
     * @var string
     */
    private $city;

    /**
     * User Region
     * @var string
     */
    private $region;

    /**
     * User CountryCode
     * @var string
     */
    private $countryCode;

    /**
     * User CountryName
     * @var string
     */
    private $countryName;

    /**
     * User ContinentCode
     * @var string
     */
    private $continentCode;

    /**
     * Geolocation constructor.
     */
    public function __construct(){
        $this->ip = self::get_ip();
        $this->fetch();
    }

    /**
     * Get User Ip
     *
     * @return string
     */
    public static function get_ip() : string {
        $ip = '';
        if (@$_SERVER['HTTP_CLIENT_IP']){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(@$_SERVER['HTTP_X_FORWARDED_FOR']){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else if(@$_SERVER['HTTP_X_FORWARDED']){
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        }else if(@$_SERVER['HTTP_FORWARDED_FOR']){
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }else if(@$_SERVER['HTTP_FORWARDED']){
            $ip = $_SERVER['HTTP_FORWARDED'];
        }else if(@$_SERVER['REMOTE_ADDR']){
            $ip = $_SERVER['REMOTE_ADDR'];
        }else{
            $ip = 'UNKNOWN';
        }
        return $ip;
    }

    /**
     * Fetch User Datas Method
     */
    private function fetch(){
        $curl = Curl::get("http://www.geoplugin.net/php.gp", array(
            "ip" => $this->ip,
        ));
		try {
			$datas = unserialize($curl);
			$this->city = $datas['geoplugin_city'];
			$this->region = $datas['geoplugin_region'];
			$this->countryCode = $datas['geoplugin_countryCode'];
			$this->countryName = $datas['geoplugin_countryName'];
			$this->continentCode = $datas['geoplugin_continentCode'];
		} catch (\Exception $e) {
			$this->city = "unknown";
			$this->region = "unknown";
			$this->countryCode = "unknown";
			$this->countryName = "unknown";
			$this->continentCode = "unknown";
		}
    }

    /**
     * Get User Ip
     * @return string
     */
    public function getIp() : string {
        return $this->ip;
    }

    /**
     * Get User City
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Get User Region
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * Get User CountryCode
     * @return string
     */
    public function getCountryCode() {
        return $this->countryCode;
    }

    /**
     * Get User CountryName
     * @return string
     */
    public function getCountryName() {
        return $this->countryName;
    }

    /**
     * Get User ContinentCode
     * @return string
     */
    public function getContinentCode() {
        return $this->continentCode;
    }
}
