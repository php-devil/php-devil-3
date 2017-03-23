<?php
namespace PhpDevil\framework\components\page\smarty;
use PhpDevil\framework\components\page\AdapterInterface;
use PhpDevil\framework\components\page\PageRendererInterface;

/**
 * Class SmartyAdapter
 * Адаптер для Smarty ^3.1.27
 * @package PhpDevil\framework\components\page\smarty\
 */
class SmartyAdapter implements AdapterInterface
{
    /**
     * Инстанс шаблонного движка
     * @var \Smarty
     */
    private $smarty;

    /**
     * Присвоение переменной представлению
     * @param $name
     * @param $value
     */
    public function assignVar($name, $value)
    {
        $this->smarty->assign($name ,$value);
    }

    public function display($view)
    {
        $this->smarty->display('view:' . $view);
    }

    public function fetch($view)
    {
        return $this->smarty->fetch('view:' . $view);
    }

    /**
     * SmartyAdapter constructor.
     * @param PageRendererInterface $component
     */
    public function __construct(PageRendererInterface $component)
    {
        $this->smarty = new \Smarty();
        if ($themeDirectory = $component->getThemeName()) $themeDirectory = '-' . $themeDirectory;
        $this->smarty->setCompileDir(\Devil::makeRuntimeDir('smarty' . $themeDirectory . '/compiled'));
        $this->smarty->setCacheDir(\Devil::makeRuntimeDir( 'smarty' . $themeDirectory . '/cache'));
        $this->smarty->addPluginsDir(__DIR__ . '/plugins');
    }
}