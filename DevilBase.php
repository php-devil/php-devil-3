<?php
namespace PhpDevil\framework;

use PhpDevil\framework\base\ApplicationException;
use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\base\helpers\FilesHelper;
use PhpDevil\framework\base\InvalidPropertyException;
use PhpDevil\framework\common\EntityType;
use PhpDevil\framework\web\Application as WebApplication;

class DevilBase
{
    private static $_application = null;

    private static $aliases = [
        '@devil' => __DIR__,
    ];

    /**
     * @return ApplicationInterface
     */
    public static function app()
    {
        return self::$_application;
    }

    public static function loadConfig($fileName)
    {
        return require $fileName;
    }

    public static function getPathOf($alias)
    {
        if (strncmp($alias, '@', 1)) return $alias;
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if (isset(self::$aliases[$root])) {
            if (is_string(self::$aliases[$root])) {
                return $pos === false ? self::$aliases[$root] : self::$aliases[$root] . substr($alias, $pos);
            }
        }
        return false;
    }

    public static function setPathOf($alias, $path, $forceOverwrite = false)
    {
        if ($forceOverwrite || !isset(self::$aliases[$alias])) {
            if (strncmp($alias, '@', 1)) {
                $alias = '@' . $alias;
            }
            self::$aliases[$alias] = str_replace('\\', '/', $path);
        }
    }

    public function hasAlias($alias)
    {
        return isset(self::$aliases[$alias]);
    }

    public static function makeRuntimeDir($innerPath)
    {
        if (!static::hasAlias('@runtime')) {
            static::setPathOf('runtime', static::getPathOf('@app') . '/runtime');
        }
        return FilesHelper::mkdir(static::getPathOf('@runtime'), $innerPath, 0777, true);
    }

    public static function registerApplication(ApplicationInterface $app)
    {
        if (null === self::$_application) {
            self::$_application = $app;
            return true;
        } else {
            throw new ApplicationException(ApplicationException::APPLICATION_TWICE_REGISTRATION);
        }
    }

    /**
     * Создание экземпляра класса фронт-контроллера приложения
     * @param array $config
     * @return ApplicationInterface
     * @throws InvalidPropertyException
     */
    public static function createApplication(array $config)
    {
        if (!isset($config['type'])) $config['type'] = 'unsupported';
        if (isset($config['class'])) {
            $className = $config['class'];
            return new $className($config);
        } else switch ($config['type']) {
            case 'web':
                return new WebApplication($config);
            default:
                throw new InvalidPropertyException([EntityType::APPLICATION, 'type']);
        }
    }

    public static function renderException(\Exception $ex)
    {
        if (null === self::$_application) {
            //todo render exception by self
        } else {
            self::$_application->renderException($ex);
        }
    }
}