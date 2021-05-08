<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8b3a733b39d2ba5631d80e67ef1b4ef4
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Spatie\\SchemaOrg\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Spatie\\SchemaOrg\\' => 
        array (
            0 => __DIR__ . '/..' . '/spatie/schema-org/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8b3a733b39d2ba5631d80e67ef1b4ef4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8b3a733b39d2ba5631d80e67ef1b4ef4::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8b3a733b39d2ba5631d80e67ef1b4ef4::$classMap;

        }, null, ClassLoader::class);
    }
}
