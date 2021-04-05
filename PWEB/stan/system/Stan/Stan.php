<?php
namespace Stan;

use Config\Config;
use Dir\DirManager;
use Cache\Cache;

class Stan {

    /**
     * Instance of Stan
     * @var Stan
     */
	private static $instance;

    /**
     * Stan constructor.
     */

    private function __construct()
    {
        self::$instance = $this;

        $this->configs = new \stdClass();
		$this->theme = json_decode(file_get_contents(APPDIR . "View" . DS . "layout" . DS . "layout.json"), false);
		
        $this->loadConfigs();
        $this->loadRoutes();
    }

    /**
     * Get the instance of Stan
     * @return Stan
     */

	public static function getInstance(): Stan {
		if(!self::$instance){
			self::$instance = new Stan();
		}
		
		return self::$instance;
	}

    /**
     * Load Configs Method
     */

	private function loadConfigs(){

        $cache = new Cache(["name" => "configs", "path" => TMPDIR . "cache/configs/", "extension" => ".cache"]);
        if (!$cache->isCached("configs")) {
            $dirManager = new DirManager();
            $cache->store("configs", $dirManager->getIni(ROOTDIR . "config"), 60*10);
        }
        $ini = $cache->retrieve("configs");

		foreach($ini as $file){
			$tmp = basename($file);
			$tmp = str_replace(".ini", "", $tmp);
			$this->configs->$tmp = new Config($file);
		}
	}

    /**
     * Load Routes Method
     */

    private function loadRoutes(){
		$dirManager = new DirManager();
		$routesFiles = $dirManager->getPhp(APPDIR . "Route");
		
		foreach($routesFiles as $file){
            require_once $file;
        }
	}
}

