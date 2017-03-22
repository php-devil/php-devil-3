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
    protected $defaultComponents = [];

    /**
     * Инициализированные компоненты
     * @var array
     */
    protected $components = [];

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
            $requiredInterface = isset($this->defaultComponents[$componentName][1])
                ? $this->defaultComponents[$componentName][1]
                : null;
            $componentConfig = isset($this->config['components'][$componentName])
                ? $this->config['components'][$componentName]
                : null;
            $componentClassName = isset($componentConfig['class'])
                ? $componentConfig['class']
                : isset($this->defaultComponents[$componentName][0])
                    ? $this->defaultComponents[$componentName][0]
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