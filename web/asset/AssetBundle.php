<?php
namespace PhpDevil\framework\web\asset;

abstract class AssetBundle implements AssetBundleInterface
{
    public static function requirements() {return [];}

    /**
     * Добавление css в head
     * @return array
     */
    public static function css() {return [];}

    /**
     * Добавление js в body
     * @return array
     */
    public static function js() {return [];}

    abstract public static function name();

    final public static function register($registerName = null)
    {
        if (null === $registerName) $registerName = static::name();
        \Devil::app()->page->addAssetBundle(static::class, $registerName);
    }

    public static function publishFiles($destination)
    {
        echo "\n\n\n publish from " . static::class . "\n";
        print_r(func_get_args());
    }
}