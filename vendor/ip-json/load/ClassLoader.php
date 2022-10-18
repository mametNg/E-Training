<?php

/**
* 
*/

namespace ipJson\Autoload;
class ClassLoader
{
	public static $vendorDir;
	public static $baseDir;
	public static $devices = "\\ip-json-php\\ip-json.php";
	public function __construct()
	{
		static::$vendorDir = (dirname(dirname(__FILE__)));
		static::$baseDir = dirname(static::$vendorDir);
	}

	public function getFile($class=false)
	{
		require $class;
	}

	public function Detector()
	{
		$this->getFile(static::$vendorDir.static::$devices);
		return $this;
	}
}