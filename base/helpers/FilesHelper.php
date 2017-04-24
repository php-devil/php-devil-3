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

    /**
     * Создание управляющего коасса для зпгрузки медиавложений моделей
     * @param $config
     * @param null $currentFilename
     * @param null $newFileData
     * @return mixed
     */
    public static function createUploadableFile($config, $currentFilename = null, $newFileData = null)
    {
        $className = '\\PhpDevil\\framework\\base\\helpers\\files\\' . ucfirst($config['type']) . 'File';
        return new $className($config, $currentFilename, $newFileData);
    }
}