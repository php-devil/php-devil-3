<?php
namespace PhpDevil\framework\components\page;
use PhpDevil\framework\components\Component;
use PhpDevil\framework\components\ComponentConfigException;
use PhpDevil\framework\components\page\smarty\SmartyAdapter;
use PhpDevil\framework\web\asset\AssetBundleInterface;

class PageRenderer extends Component implements PageRendererInterface
{
    /**
     * Адаптер шаблонного движка
     * @var PageRendererInterface
     */
    protected $adapter = null;

    /**
     * Класс, отображающий представление
     * @var Renderable
     */
    protected $renderer = null;

    /**
     * Менеджер статических ресурсов
     * @var null
     */
    protected $assets = null;

    /**
     * Поддерживаемые адаптеры
     * @var array
     */
    private static $adaptersAvailable = [
        'smarty' => SmartyAdapter::class,
    ];

    public function addAssetBundle($className, $registerName)
    {
        if (null === $this->assets) $this->assets = new AssetManager;
        $this->assets->publish($className, $registerName);
    }

    public function __set($name, $value)
    {
        $this->createAdapter();
        $this->adapter->assignVar($name, $value);
    }

    /**
     * Доступ к классу, вызвавшему отображение представления
     * @return Renderable
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    public function getThemeName()
    {
        return null;
    }

    public function css($value = null) {
        if (null === $this->assets) $this->assets = new AssetManager;
        if (null === $value) {
            return $this->assets->css();
        }
    }

    public function js($value = null) {
        if (null === $this->assets) $this->assets = new AssetManager;
        if (null === $value) {
            return $this->assets->js();
        }
    }

    /**
     * Вывод (возврат) представления
     * @param Renderable $renderer
     * @param $view
     * @param null $argv
     * @param bool $displayImmediately
     */
    public function render(Renderable $renderer, $view, $argv = null, $displayImmediately = false)
    {
        $this->renderer = $renderer;
        $this->createAdapter();
        if (is_array($argv) && !empty($argv)) foreach ($argv as $k=>$v) {
            $this->adapter->assignVar($k, $v);
        }
        $this->adapter->assignVar('application', \Devil::app());
        $this->adapter->assignVar('this', $this->renderer);
        if ($displayImmediately) {
            $this->adapter->display($view);
        } else {
            return $this->adapter->fetch($view);
        }
    }

    /**
     * Инициализация шаблонного движка
     * @throws ComponentConfigException
     */
    private function createAdapter()
    {
        if (null === $this->adapter) {
            if (isset(static::$adaptersAvailable[$this->config['engine']])) {
                $rendererClassName = static::$adaptersAvailable[$this->config['engine']];
                $this->adapter = new $rendererClassName($this);
            } else {
                throw new ComponentConfigException([
                    ComponentConfigException::INVALID_PARAMETER_VALUE,
                    $this, 'engine', $this->config['engine']
                ]);
            }
        }
    }
}