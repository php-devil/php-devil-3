<?php
namespace PhpDevil\framework\base\helpers;

class FilesHelper
{
    /**
     * Создание поддиректории в заданном пути
     * @param $basePath
     * @param $innerPath
     * @param $mode
     * @param bool $recursive
     * @return mixed
     */
    public static function mkdir($basePath, $innerPath, $mode, $recursive = true)
    {
        $fullDirectoryPath = str_replace('\\', '/', str_replace('//', '/', $basePath . '/' . $innerPath));
        if (!is_dir($fullDirectoryPath)) {
            mkdir($fullDirectoryPath, $mode, $recursive);
            return $fullDirectoryPath;
        } else {
            return $fullDirectoryPath;
        }
    }
}