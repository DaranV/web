<?php
namespace Dir;

use Logs\Logging;

class DirManager
{
    /**
     * Founded Files
     * @var array
     */
    private $result = array();

    /**
     * Search Ini Files
     *
     * @param string $src
     * @return array
     */

	public function getIni(string $src) : array {
		$dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->getIni($src . '/' . $file);
                }
                else {
					if (preg_match('@.ini@', $file)) {
						array_push($this->result, $src . '/' . $file);
					}
                }
            }
        }
        closedir($dir);
		return $this->result;
	}

    /**
     * Search Sql Files
     *
     * @param string $src
     * @return array
     */

	public function getSql(string $src) : array {
		$dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->getSql($src . '/' . $file);
                }
                else {
					if (preg_match('@.sql@', $file)) {
						array_push($this->result, $src . '/' . $file);
					}
                }
            }
        }
        closedir($dir);
		return $this->result;
	}

    /**
     * Search Json Files
     *
     * @param string $src
     * @return array
     */

	public function getJson(string $src) : array {
		$dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->getJson($src . '/' . $file);
                }
                else {
					if (preg_match('@.json@', $file)) {
						array_push($this->result, $src . '/' . $file);
					}
                }
            }
        }
        closedir($dir);
		return $this->result;
	}
	
	
    /**
     * Search Php Files
     *
     * @param string $src
     * @return array
     */
	
	public function getPhp(string $src) : array {
		$dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->getJson($src . '/' . $file);
                }
                else {
					if (preg_match('@.php@', $file)) {
						array_push($this->result, $src . '/' . $file);
					}
                }
            }
        }
        closedir($dir);
		return $this->result;
	}


    /**
     * List File In Directory
     *
     * @param string $src
     * @return array
     */

	public function listFiles(string $src) : array {
		$dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->listFiles($src . '/' . $file);
                }
                else {
					array_push($this->result, $src . '/' . $file);
                }
            }
        }
        closedir($dir);
		return $this->result;
	}

    /**
     * Search Files In Directory By Extension
     *
     * @param $src
     * @param $search
     * @return array
     */

    public function search(string $src, string $search) : array{
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->search($src . '/' . $file);
                }
                else {
                    if (preg_match('@'.$search.'@', $file)) {
                        array_push($this->result, $src . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
        return $this->result;
    }
}

?>