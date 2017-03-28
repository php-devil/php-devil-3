<?php
namespace PhpDevil\framework\components\page;

class AssetManager
{
    private $destination = null;

    private $assetUrl = null;

    private $registeredBundles = [];

    private $registeredNames = [];

    private $stackBundles = [];

    private $css = [];

    private $js  = [];

    public function css()
    {
        return $this->css;
    }

    public function js()
    {
        return $this->js;
    }

    public function getBundle($name)
    {
        if (isset($this->registeredNames[$name])) {
            return $this->registeredNames[$name];
        } else {
            return null;
        }
    }

    private function flushStack()
    {
        if (!empty($this->stackBundles)) foreach ($this->stackBundles as $class=>$name) {
            if ($css = $class::css()) foreach ($css as $link) {
                if (0 === strpos($link, '//')) $this->css[] = $link;
                else $this->css[] = [$name, $link];
            }
            if ($js = $class::js()) foreach ($js as $link) {
                if (0 === strpos($link, '//')) $this->js[] = $link;
                else $this->js[] = [$name, $link];
            }
            $this->registeredBundles[$class] = $name;
            // todo: срубать совпадение имен
            $this->registeredNames[$name] = $class;
            unset($this->stackBundles[$name]);
        }
    }

    private function registerBundle($asset, $name)
    {
        if (isset($this->registeredBundles[$asset]) || isset($this->stackBundles[$asset])) return false;
        if ($required = $asset::requirements()) foreach($required as $subBundle) {
            $this->registerBundle($subBundle, $subBundle::name());
        }
        $this->stackBundles[$asset] = $name;
        return true;
    }

    public function publish($asset, $name)
    {
        if ($this->registerBundle($asset, $name)) $this->flushStack();
    }

    public function __construct()
    {
        if (!\Devil::hasAlias('@assets')) \Devil::setPathOf('assets', \Devil::getPathOf('@http') . '/assets');
        if (!\Devil::hasAlias('@assetsUrl')) \Devil::setPathOf('assetsUrl', \Devil::getPathOf('@httpUrl') . 'assets');

        $this->destination = \Devil::getPathOf('@assets');
        $this->assetUrl    = \Devil::getPathOf('@assetsUrl');

    }
}