<?php
namespace PhpDevil\framework\base;

use PhpDevil\framework\components\InvalidInterfaceException;
use PhpDevil\framework\components\UnknownComponentException;
use PhpDevil\framework\components\weburl\WebUrl;
use PhpDevil\framework\components\weburl\WebUrlInterface;

abstract class ModulePrototype extends ControllerPrototype
{
    /**
     * Предопределенные компоненты веб-приложения, требования интерфейсов к основным компонентам
     * При отсутствии параметров в конфигурации приложения при ображении будут созданы
     * с параметрами по умолчанию
     * @var array
     */
    protected static $defaultComponents = [];

    /**
     * Известные модулю (приложению) модели с короткими именами
     * @var array
     */
    protected static $models = [];

    /**
     * Известные модулю (приложению) модели с короткими именами
     * @var array
     */
    protected static $controllers = [];

    /**
     * Инициализированные компоненты
     * @var array
     */
    protected $components = [];

    /**
     * Проверка наличия модели в данном модуле (приложении)
     * @param $shortName
     * @return bool
     */
    public static function hasModel($shortName)
    {
        return isset(static::$models[$shortName]);
    }

    /**
     * Проверка наличия контроллера в данном модуле (приложении)
     * @param $shortName
     * @return bool
     */
    public static function hasController($shortName)
    {
        return isset(static::$controllers[$shortName]);
    }

    /**
     * Создание компонента по имени
     * @param $componentName
     * @return mixed
     * @throws InvalidInterfaceException
     * @throws UnknownComponentException
     */
    public function callComponent($componentName)
    {
        if (!isset($this->components[$componentName])) {
            $requiredInterface = isset(static::$defaultComponents[$componentName][1])
                ? static::$defaultComponents[$componentName][1]
                : null;
            $componentConfig = isset($this->config['components'][$componentName])
                ? $this->config['components'][$componentName]
                : null;
            $componentClassName = isset($componentConfig['class'])
                ? $componentConfig['class']
                : isset(static::$defaultComponents[$componentName][0])
                    ? static::$defaultComponents[$componentName][0]
                    : null;
            unset($this->config['components'][$componentName]);
            unset($componentConfig['class']);
            if ($componentClassName) {
                $instance = new $componentClassName($componentConfig, $this);
                if (null === $requiredInterface || ($instance instanceof $requiredInterface)) {
                    $this->components[$componentName] = $instance;
                } else {
                    throw new InvalidInterfaceException([$componentName, $componentClassName, $requiredInterface]);
                }
            } else {
                throw new UnknownComponentException($componentName);
            }
        }
        return $this->components[$componentName];
    }
}