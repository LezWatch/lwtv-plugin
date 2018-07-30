<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83a562e253d7cada8016df3097711aa7
{
    public static $files = array (
        '10e2bd795825f9abb125ae7cc3251364' => __DIR__ . '/..' . '/webdevstudios/cmb2/init.php',
        'c78ad1557f9d49496e9cace7fb7c11fc' => __DIR__ . '/..' . '/origgami/cmb2-grid/Cmb2GridPlugin.php',
        '2785dcb5a0c9bd334a630e7b99cb65b1' => __DIR__ . '/..' . '/podkot/cmb-field-select2/cmb-field-select2.php',
        '38c4b14ab59330b8abbc748a6182c696' => __DIR__ . '/..' . '/level-level/facetwp-wp-cli/facetwp-wp-cli.php',
        '2575dc2315d7e291c5b44d25ca240c12' => __DIR__ . '/..' . '/webdevstudios/facetwp-cmb2/cmb2.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83a562e253d7cada8016df3097711aa7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83a562e253d7cada8016df3097711aa7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
