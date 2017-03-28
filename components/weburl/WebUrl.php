<?php
namespace PhpDevil\framework\components\weburl;
use PhpDevil\framework\components\Component;

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
        if (is_array($this->modulesRequests)) foreach ($this->modulesRequests as $url=>$id) {
            if (0 === strpos($unused, $url)) {
                return $id;
            }
        }
        return null;
    }

    public function useModule($id)
    {
        $this->request->setAsUsed($this->modulesUrls[$id]);
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
     * Добавление модуля в список известных запросов
     * @param $configID
     * @param $mountPoint
     */
    private function addModuleRequest($configID, $mountPoint)
    {
        $this->modulesUrls[$configID] = $mountPoint;
        $this->modulesRequests[$mountPoint] = $configID;
    }

    /**
     * Сбор известных данных о смонтированных точках подключения модулей
     * и контроллеров
     */
    protected function initAfterConstruct()
    {
        $this->request = new Request();
        if ($modules = $this->owner->getConfig('modules')) {
            $this->getAutoMounting();
            foreach ($modules as $k=>$conf) {
                if ('auto' === $conf['mount']) {
                    //todo: монтировать из модели
                } elseif (isset($conf['mount']) && !empty($conf['mount'])) {
                    $this->addModuleRequest($k, $conf['mount']);
                }
            }
        } else {
            echo 'owner has not modules';
        }
        krsort($this->modulesRequests);
    }
}