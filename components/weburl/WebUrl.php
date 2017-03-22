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