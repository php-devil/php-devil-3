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
        $models = static::models();
        return isset($models[$shortName]);
    }

    /**
     * Проверка наличия контроллера в данном модуле (приложении)
     * @param $shortName
     * @return bool
     */
    public static function hasController($shortName)
    {
        $controllers = static::controllers();
        return isset($controllers[$shortName]);
    }

    /**
     * Загрузка контроллера
     * @param $controller
     * @param null $config
     * @return mixed
     */
    public function loadController($controller, $config = null)
    {
        $tagName = null;
        $controllers = static::controllers();
        if (isset($controllers[$controller])) {
            $className = $controllers[$controller];
            $tagName = $controller;
        } else {
            $className = $this->getNamespace() . '\\controllers\\' . \Devil::app()->url->classNameFromUrl($controller).'Controller';
        }
        $controller = new $className($config, $this);
        if (null === $tagName) {
            $tagName = substr($className, intval(strrpos($className, '\\') + 1), - 10);
        }
        $controller->setTagName($tagName);
        return $controller;
    }

    /**
     * Запуск модуля на выполнение.
     * Сценарий по умолчанию - первое вхождение урла - контроллер, второе - действие.
     * Если вхождение одно (контроллер) - запускается actionIndex()
     */
    public function run()
    {
        $controllerName = \Devil::app()->url->nextUrlToController();
        if (null === $controllerName) {
            $controllerName = 'Site';
        }
        $actionName = \Devil::app()->url->nextUrlToAction();
        if (null === $actionName) {
            if ($this instanceof ApplicationInterface) {
                $actionName = 'Index';
            } else {
                $actionName = 'Default';
            }

        }
        $this->runControllerAction($controllerName, $actionName);
    }

    public function runControllerAction($controller, $action)
    {
        $this->loadController($controller)->performAction($action);
    }

    /**
     * В качестве свойств фронт-контроллера приложения используются компоненты
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->callComponent($name);
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
            $componentConfig = isset(static::$defaultComponents[$componentName][2])
                ? static::$defaultComponents[$componentName][2]
                : [];
            if (isset($this->config['components'][$componentName])){
                $componentConfig = array_merge($componentConfig, $this->config['components'][$componentName]);
            }
            if (isset($componentConfig['class'])) {
                $componentClassName = $componentConfig['class'];
            } else {
                $componentClassName = isset(static::$defaultComponents[$componentName][0])
                    ? static::$defaultComponents[$componentName][0]
                    : null;
            }
            unset($this->config['components'][$componentName]);
            if (isset($componentConfig['class']))
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

    public function execute($commandName, $params = [])
    {
        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $commandName))) . 'Command';
        $clearCommandName = $this->getNamespace() . "\\console\\" . $className;
        if (!class_exists($clearCommandName)) $clearCommandName = '\\app\\console\\' . $className;
        if (!class_exists($clearCommandName)) $clearCommandName = 'PhpDevil\\framework\\console\\commands\\' . $className;
        if (!class_exists($clearCommandName)) die("\nCommand " . $commandName . " not found");
        (new $clearCommandName)->execute($params);
    }
}