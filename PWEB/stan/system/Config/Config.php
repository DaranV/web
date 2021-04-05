<?php
namespace Config;

use Stan\Stan;
use Cache\Cache;

class Config
{
    /**
     * The name of the config file
     *
     * @var string
     */
	private $_iniFilename = "";

    /**
     *
     * @var array
     */
	private $_iniParsedArray = array();

    /**
     * Config constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $stan = Stan::getInstance();
        switch ($filename) {
            case "database":
                $filename = ROOTDIR . "config" . DS . "database.ini";
                break;
            case "config":
                $filename = ROOTDIR . "config" . DS . "config.ini";
                break;
        } //$filename
        $this->_iniFilename = $filename;

        $cache_name = "config_" . $filename;
        $cache = new Cache(["name" => $cache_name, "path" => TMPDIR . "cache/configs/", "extension" => ".cache"]);
        if (!$cache->isCached($cache_name)) {
            $cache->store($cache_name, parse_ini_file($filename, true), 60*5);
        }
        $this->_iniParsedArray = $cache->retrieve($cache_name);
    }

    /**
     * Get Data
     *
     * @param string $key
     * @return string
     */
    public function get(string $key) : string
    {
        return $this->_iniParsedArray[$key];
    }

    /**
     * Set Data
     *
     * @param string $key
     * @param mixed|NULL $value
     * @return bool
     */
	public function set(string $key, mixed $value = NULL) : bool
    {
        return $this->_iniParsedArray[$key] = $value;
    }

    /**
     * Remove Data
     *
     * @param string $key
     */

    public function remove(string $key)
    {
        unset($this->_iniParsedArray[$key]);
    }

    /**
     * Save Datas
     *
     * @param string|null $filename
     * @return bool
     */

    public function save(string $filename = null) : bool
    {
        $stan = Stan::getInstance();
        if ($filename == null)
            $filename = $this->_iniFilename;
        if (is_writeable($filename)) {
            $file = fopen($filename, "w");
            foreach ($this->_iniParsedArray as $key => $value) {
                fwrite($file, "$key = $value\n");
            }
            fclose($file);
            return true;
        }
        else {
            return false;
        }
    } 
	
}
?>