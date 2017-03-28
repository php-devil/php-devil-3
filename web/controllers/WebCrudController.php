<?php
namespace PhpDevil\framework\web\controllers;

use PhpDevil\framework\models\ModelInterface;
use PhpDevil\framework\web\widgets\FormWidget;

class WebCrudController extends WebController
{
    protected $isAjaxRequest = false;

    private static $uniqueBlockID = 0;

    final public static function getUniqueID()
    {
        ++ self::$uniqueBlockID;
        return 'auto_gen_id_' . self::$uniqueBlockID;
    }

    /**
     * Блок произвольной формы с возможностью перерисовки по ajax запросу
     * @param ModelInterface $model
     * @param array $config
     */
    public function ajaxForm(ModelInterface $model, $action, $config = null)
    {
        $view = isset($config['view']) ? $config['view'] : '//widgets/ajax_form';
        if (!isset($config['id'])) {
            $config['id'] = static::getUniqueID();
        }
        $formView = null;
        if ($model->accessControl($action)) {
            $formView = new FormWidget($model, $config);

            // todo: post

        } else {
            $view = '//widgets/access_denied';
        }

        $content = $this->render($view, [
            'block_id' => $config['id'],
            'widget_id' => 'ajaxForm',
            'dataAction' => $action,
            'form' => $formView,
            'model_block_full_id' => str_replace('\\', '__', get_class($model)),
            'widget_start_params' => json_encode($config),
            'renderMode' => $this->getRenderMode(),
        ], !$this->isAjaxRequest);

        if ($this->isAjaxRequest) {
            return $content;
        }
    }

    public function getRenderMode()
    {
        return $this->isAjaxRequest ? 'inner' : 'default';
    }
}