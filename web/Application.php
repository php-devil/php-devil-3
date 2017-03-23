<?php
namespace PhpDevil\framework\web;

use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\components\page\PageRenderer;
use PhpDevil\framework\components\page\PageRendererInterface;
use PhpDevil\framework\components\page\Renderable;
use PhpDevil\framework\components\weburl\WebUrl;
use PhpDevil\framework\components\weburl\WebUrlInterface;
use PhpDevil\framework\components\webuser\User;
use PhpDevil\framework\components\webuser\UserInterface;
use PhpDevil\framework\web\http\HttpException;

/**
 * Class Application
 *
 * @package PhpDevil\framework\web
 *
 * @property WebUrlInterface $url
 * @property UserInterface   $user
 */
class Application extends \PhpDevil\framework\base\Application implements ApplicationInterface, Renderable
{
    /**
     * Предопределенные компоненты веб-приложения, требования интерфейсов к основным компонентам
     * При отсутствии параметров в конфигурации приложения при ображении будут созданы
     * с параметрами по умолчанию
     *
     * имя свойства/компонента => array(класс [, интерфейс [, дефолтный конфиг]])
     *
     * @var array
     */
    protected static $defaultComponents = [
        'url'   => [WebUrl::class,       WebUrlInterface::class],
        'user'  => [User::class,         UserInterface::class],
        'page'  => [PageRenderer::class, PageRendererInterface::class, ['engine' => 'smarty']],
    ];

    /**
     * В качестве свойств фронт-контроллера приложения используются компоненты
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->callComponent($name);
    }

    public function getViewsLocation()
    {
        return \Devil::getPathOf('@app') . '/views';
    }

    public static function controllers()
    {
        return null;
    }

    public function getNamespace()
    {
        return '\\app';
    }

    /**
     * У фронт-контроллера приложения не может быть тега
     * @return null
     */
    public function getTagName()
    {
        return null;
    }

    /**
     * Сценарий выполнения приложения:
     * - если путь начинается с адреса модуля - загрузка модуля и передача управления ему,
     * - если нет - попытка найти и выполнить действие контроллера
     * @throws HttpException
     */
    final public function run()
    {
        \Devil::registerApplication($this);
        if ($moduleID = $this->url->isModuleRequested()) {
            if ($module = $this->loadModule($moduleID)) {
                if ($module->beforeRun()) {
                    $this->url->useModule($moduleID);
                    $module->run();
                    $module->afterRun();
                } else {
                    $module->errorRun();
                }
            } else {
                throw new HttpException(HttpException::NOT_FOUND);
            }
        } else {
           parent::run();
        }
    }
}