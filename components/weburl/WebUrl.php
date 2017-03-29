<?php
namespace PhpDevil\framework\components\weburl;
use PhpDevil\framework\components\Component;
use PhpDevil\framework\containers\Modules;

/**
 * Class WebUrl
 * Дефолтный роутинг для веб-приложения
 * @package PhpDevil\framework\components\weburl
 */
class WebUrl extends Component implements WebUrlInterface
{
    private $request = null;

    private $modulesUrls = null;

    private $modulesRequests = null;

    /**
     * Если запрос пользователя начинается с точки монтирования модуля,
     * возвращает его идентификатор в том виде, как указано в конфигурации приложения
     * @return string|null
     */
    public function isModuleRequested()
    {
        $unused = '/' . $this->request->getUnusedUri();
        return Modules::container()->getTagByUrl($unused);
    }

    public function getModuleUrl($tagName)
    {
        return Modules::container()->getUrlByTag($tagName);
    }

    public function useModule($tagName)
    {
        $this->request->setAsUsed(Modules::container()->getUrlByTag($tagName));
    }

    /**
     * Преобразование следующего вхождения запроса
     * в CamelCase. При отсутствии следующего вхождения вернет null
     */
    public function classNameFromUrl($urlPart = null)
    {
        if (null === $urlPart) $urlPart = $this->request->getNext();
        if ($urlPart) {
            return str_replace(' ', '', ucwords(str_replace('-', ' ', $urlPart)));
        } else {
            return null;
        }
    }

    public function getUsed()
    {
        return $this->request->getUsed();
    }

    public function getNext()
    {
        return $this->request->getNext();
    }

    public function nextUrlToController($nameSpace = null)
    {
        return $this->request->getNext();
    }

    public function nextUrlToAction()
    {
        return $this->classNameFromUrl();
    }

    /**
     * Получение списка динамически монтируемых модулей.
     * В конфигурации компонента должна быть указана модель, отвечающая за
     * url адреса модулей (как правило - модель структуры сайта)
     */
    protected function getAutoMounting()
    {
        if ($this->getConfig('mountModel')) {
            // todo проверка модели на соответствие интерфейса, выгрузка пар ID=>URL смонтированных модулей
        }
    }

    /**
     * Сбор известных данных о смонтированных точках подключения модулей
     * и контроллеров
     */
    protected function initAfterConstruct()
    {
        $this->request = new Request();
        if (!Modules::container()->isEmpty()) {
            $this->getAutoMounting();
        }
    }
}