<?php
namespace PhpDevil\framework\base;
use PhpDevil\framework\components\InvalidInterfaceException;
use PhpDevil\framework\components\UnknownComponentException;
use PhpDevil\framework\components\weburl\WebUrl;
use PhpDevil\framework\components\weburl\WebUrlInterface;
use PhpDevil\framework\containers\Modules;
use PhpDevil\framework\helpers\NamesHelper;
use PhpDevil\framework\web\http\HttpException;

/**
 * Class ModulePrototype
 *
 * Прототип модуля.
 * Назначение основных свойств, сценарий запуска выполнения модуля по умолчанию для консольного
 * и веб приложений.
 *
 * @package PhpDevil\framework\base
 */
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
     * Уже загруженные контроллеры дянного модуля
     * @var array
     */
    private $_loadedControllers = [];

    /**
     * Проверка наличия модели в данном модуле (приложении)
     * @param $shortName
     * @return bool
     */
    public static function hasModel($shortName, $instaitiateOnTrue = false)
    {
        $models = static::models();
        if (isset($models[$shortName])) {
            if ($instaitiateOnTrue) {
                $className = $models[$shortName];
                return $className::model();
            } else {
                return true;
            }
        }
        return false;
    }

    public static function loadModel($shortName)
    {
        return static::hasModel($shortName, true);
    }

    /**
     * Корневая директория модуля (определяется расположением фронт-контроллера)
     * @return mixed|null
     */
    public function getLocation()
    {
        return Modules::container()->getLocationByClassName(get_class($this));
    }

    /**
     * Тег модуля (ключ в массиве конфигурации приложения)
     * @return mixed|null
     */
    public function getTagName()
    {
        return Modules::container()->getTagByClassName(get_class($this));
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
    public function loadController($controller)
    {
        if (!isset($this->_loadedControllers[$controller])) {
            $controllers = static::controllers();
            if (isset($controllers[$controller])) {
                $className = $controllers[$controller];
                $tagName = $controller;
            } else {
                $className = $this->getNamespace() . '\\controllers\\' . NamesHelper::urlToClass($controller) . 'Controller';
            }
            if (class_exists($className)) {
                $instance = new $className([], $this);
                $instance->setTagName($controller);
                $this->_loadedControllers[$controller] = $instance;
            }
        }
        return $this->_loadedControllers[$controller];
    }

    /**
     * Запуск модуля на выполнение (для веб-приложения).
     * Сценарий по умолчанию - первое вхождение урла - контроллер, второе - действие.
     * Если вхождение одно (контроллер) - запускается actionIndex()
     */
    public function run()
    {
        if (!($controller = \Devil::app()->url->getNext())) {
            $controller = 'site';
        }
        if ($controller = $this->loadController($controller)) {
            if (!$nextUrl = \Devil::app()->url->getNext()) {
                $nextUrl = 'index';
            }
            $controller->performAction(NamesHelper::urlToClass($nextUrl));
        } else {
            throw new HttpException(HttpException::NOT_FOUND);
        }
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

    /**
     * Аналог метода run() для консольного приложения
     * @param $commandName
     * @param array $params
     */
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