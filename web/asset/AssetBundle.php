<?php
namespace PhpDevil\framework\web\asset;

abstract class AssetBundle implements AssetBundleInterface
{
    const AS_IS = 0;

    public static function requirements() {return [];}

    /**
     * Добавление css в head
     * @return array
     */
    public static function css() {return [];}

    /**
     * Параметры публикаци файлов
     * @return null
     */
    public static function source()
    {
        return dirname((new \ReflectionClass(static::class))->getFileName());
    }

    public static function dest()
    {
        return static::name();
    }

    /**
     * Добавление js в body
     * @return array
     */
    public static function js() {return [];}

    public static function files() {return [];}

    abstract public static function name();

    final public static function publishFile($source)
    {
        $files = static::files();
        if (isset($files[$source])) {
            $sourceFile = static::source() . '/' . $source;

            if (file_exists($sourceFile)) {
                $sourceTime = filemtime($sourceFile);
                if ($destDir = static::dest()) {
                    $destUrl = $destDir . '/' . $source;
                    $destFile = \Devil::getPathOf('@assets') . '/' . $destUrl;
                    if (file_exists($destFile)) {
                        $destTime = filemtime($destFile);
                    } else {
                        $destTime = 0;
                    }
                    if ($sourceTime > $destTime) {
                        $destDir = dirname($destFile);
                        if (!is_dir(dirname($destFile))) mkdir(dirname($destFile), 0777, true);
                        copy($sourceFile, $destFile);
                    }
                }
            }
            return $destUrl;
        } else {
            return null;
        }

    }

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