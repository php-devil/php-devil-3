<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\models\ModelInterface;
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
                $currentSet[$attr] = $this->model->$attr;
            }
        }
        return $currentSet;
    }

    public function getCommonControls()
    {
        if (isset($this->config['controls'])) {
            return $this->config['controls'];
        } else {
            return null;
        }
    }

    public function hasSections()
    {
        return isset($this->config['sections']);
    }

    public function __construct(ModelInterface $model, $config = [])
    {
        $this->model = $model;
        $this->config = $config;
    }
}