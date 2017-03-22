<?php
namespace PhpDevil\framework;

use PhpDevil\framework\base\ApplicationException;
use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\base\InvalidPropertyException;
use PhpDevil\framework\common\EntityType;
use PhpDevil\framework\web\Application as WebApplication;

class DevilBase
{
    private static $_application = null;

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