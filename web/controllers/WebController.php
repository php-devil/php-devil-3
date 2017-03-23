<?php
namespace PhpDevil\framework\web\controllers;
use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\base\Controller;
use PhpDevil\framework\base\ModulePrototype;
use PhpDevil\framework\components\page\Renderable;

class WebController extends Controller implements Renderable
{
    public function getViewsLocation()
    {
        if ($this->owner instanceof ApplicationInterface) {
            return \Devil::getPathOf('@app') . '/views';
        } else {
            return str_replace('\\', '/', $this->owner->getLocation() . '/views');
        }
    }

    public function render($view, $attributes = [])
    {
        if (false === strpos($view, '//')) {
            if ($tag = $this->getTagName()) $view = $tag . '/' . $view;
        } else {
            $view = substr($view, 2);
        }
        \Devil::app()->page->render($this, $view, $attributes, true);
    }
}