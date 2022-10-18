<?php

/**
* 
*/
class ipJsonAutoLoader
{
	private static $loader;

	public static function loadClassLoader($class)
    {
        if ('ipJson\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

	public static function getLoader($value='')
	{
		if (null !== self::$loader) {
            return self::$loader;
        }
        
		require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ipJsonAutoLoader', 'loadClassLoader'), true, true);

        self::$loader = $loader = new \ipJson\Autoload\ClassLoader(\dirname(\dirname(__FILE__)));

        self::$loader->Detector();

        return $loader;
	}
	
}