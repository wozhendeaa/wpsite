<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfa149f11c27d1602df8d848b9989bf1d
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\MOBILEAPIAUT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\MOBILEAPIAUT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-mobile-api/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfa149f11c27d1602df8d848b9989bf1d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfa149f11c27d1602df8d848b9989bf1d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
