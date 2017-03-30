<?php
namespace PhpDevil\framework\web\controllers;
use PhpDevil\framework\base\ApplicationInterface;
use PhpDevil\framework\base\Controller;
use PhpDevil\framework\base\ModulePrototype;
use PhpDevil\framework\components\page\Renderable;
use PhpDevil\framework\models\ModelInterface;
use PhpDevil\framework\web\widgets\FormWidget;
use PhpDevil\framework\web\widgets\GridWidget;

class WebController extends Controller implements Renderable
{
    /**
     * Счетчик генератора уникальных ID блоков страницы
     * @var int
     */
    private static $uniqueBlockID = 0;

    /**
     * Сгенерировать новый уникальный ID для блока
     * @return string
     */
    final public static function getUniqueBlockID()
    {
        ++ self::$uniqueBlockID;
        return 'page_block_' . self::$uniqueBlockID;
    }

    /**
     * Путь к директории представлений
     * @return mixed|string
     */
    public function getViewsLocation()
    {
        if ($this->owner instanceof ApplicationInterface) {
            return \Devil::getPathOf('@app') . '/views';
        } else {
            return str_replace('\\', '/', $this->owner->getLocation() . '/views');
        }
    }

    public function formWidget(ModelInterface $model, $subAction, $itemID, $config = null)
    {
        $widget = null;
        if (null === $subAction || $model->accessControl($subAction)) {
            $view = '//widgets/forms/default';
            if (isset($config['view'])) $view = $config['view'];
            if (!isset($itemID) || $model->findOne($itemID)) {
                $widget = new FormWidget($model, $config);
            } else {
                $view = '//widgets/errors/404';
            }
        } else {
            $view = '//widgets/errors/403';
        }
        ob_start();
        $this->render($view, ['form' => $widget]);
        return ob_get_clean();
    }

    public function gridWidget(ModelInterface $model, $subAction, $config = null)
    {
        $widget = null;
        if (null === $subAction || $model->accessControl($subAction)) {
            $view = '//widgets/grids/default';
            if (isset($config['view'])) $view = $config['view'];
            $widget = new GridWidget($model, $config);
        } else {
            $view = '//widgets/errors/403';
        }
        ob_start();
        $this->render($view, ['grid' => $widget]);
        return ob_get_clean();
    }

    public function render($view, $attributes = [], $display = true)
    {
        if (false === strpos($view, '//')) {
            if ($tag = $this->getTagName()) $view = $tag . '/' . $view;
        } else {
            $view = substr($view, 2);
        }
        return \Devil::app()->page->render($this, $view, $attributes, $display);
    }
}