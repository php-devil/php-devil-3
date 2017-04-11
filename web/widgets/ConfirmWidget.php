<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\web\WebWidget;

class ConfirmWidget extends WebWidget
{
    protected $model;

    protected $param;

    protected $text = 'Подтвердите действие';

    protected $allowed = false;

    public function getText()
    {
        return $this->text;
    }

    public function getLinkOk()
    {
        return $this->param['link_ok'];
    }

    public function getIcon()
    {
        return '<i class="fa fa-3x fa-' . $this->param['icon'] . '"></i>';
    }

    public function __construct($model, $subAction, $param = [])
    {
        $this->model = $model;
        $this->param = $param;
        if ($model->accessControl($subAction)) {
            $this->allowed = true;
            $this->text = $param['text_ok'];
        } else {
            $this->text = $param['text_ko'];
        }
    }
}