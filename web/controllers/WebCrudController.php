<?php
namespace PhpDevil\framework\web\controllers;

use PhpDevil\framework\models\ModelInterface;
use PhpDevil\framework\web\widgets\FormWidget;

class WebCrudController extends WebController
{
    protected $isAjaxRequest = false;

    /**
     * Блок произвольной формы с возможностью перерисовки по ajax запросу
     * @param ModelInterface $model
     * @param array $config
     */
    public function ajaxForm(ModelInterface $model, $config = [])
    {
        $view = isset($config['view']) ? $config['view'] : '//widgets/ajax_form';

        $formView = new FormWidget($model, $config);

        $this->render($view ,[
            'widget_id'            => 'ajaxForm',
            'form'                 => $formView,
            'model_block_full_id'  => str_replace('\\', '__', get_class($model)),
            'widget_start_params'  => json_encode($config),
            'renderMode'           => $this->getRenderMode(),
        ]);
    }

    public function getRenderMode()
    {
        return $this->isAjaxRequest ? 'inner' : 'default';
    }
}