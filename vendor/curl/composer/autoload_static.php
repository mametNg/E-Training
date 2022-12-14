<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf820e0918de3ceed26fc160f8fd8d414
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf820e0918de3ceed26fc160f8fd8d414::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf820e0918de3ceed26fc160f8fd8d414::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf820e0918de3ceed26fc160f8fd8d414::$classMap;

        }, null, ClassLoader::class);
    }
}
