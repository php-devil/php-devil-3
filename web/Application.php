<?php
namespace PhpDevil\framework\web;

use PhpDevil\framework\components\weburl\WebUrl;
use PhpDevil\framework\components\weburl\WebUrlInterface;

class Application extends \PhpDevil\framework\base\Application
{
    /**
     * Предопределенные компоненты веб-приложения, требования интерфейсов к основным компонентам
     * При отсутствии параметров в конфигурации приложения при ображении будут созданы
     * с параметрами по умолчанию
     * @var array
     */
    protected $defaultComponents = [
        'url' => [WebUrl::class, WebUrlInterface::class],
    ];

    public function __get($name)
    {
        return $this->callComponent($name);
    }

    final public function run()
    {
        \Devil::registerApplication($this);
        if ($moduleID = $this->url->isModuleRequested()) {
            echo $moduleID;
        }
    }
}