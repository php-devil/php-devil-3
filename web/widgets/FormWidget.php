<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\web\WebWidget;

/**
 * Class FormWidget
 * Виджет отображения формы
 * @package PhpDevil\framework\web\widgets
 */
class FormWidget extends WebWidget
{
    protected $model;

    protected $config;

    public function getFieldSet($sectionID = null)
    {
        $currentSet = [];
        if (null === $sectionID) {
            if ($this->hasSections()) die('WHEN USING SECTION FORM NEED TO SPECIFY SECTION');
            if (!isset($this->config['attributes'])) $this->config['attributes'] = array_keys(get_class($this->model)::attributes());
            foreach ($this->config['attributes'] as $attr) {
                $currentSet[$attr] = $this->attr($attr);
            }
        }
        return $currentSet;
    }

    public function attr($attr)
    {
        return $this->model->$attr;
    }


    public function getCommonControls()
    {
        if (isset($this->config['formControls'])) {
            return $this->config['formControls'];
        } else {
            return null;
        }
    }

    public function hasSections()
    {
        return isset($this->config['sections']);
    }

    public function getSections()
    {
        return $this->config['sections'];
    }

    public function __construct($model, $config = [], $isNew = false)
    {
        $this->model = $model;
        $this->config = $config;
    }
}