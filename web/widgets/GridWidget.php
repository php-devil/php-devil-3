<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\web\WebWidget;
use PhpDevil\framework\models\ModelInterface;

/**
 * Class GridWidget
 * Табличное представление данных
 * @package PhpDevil\framework\web\widgets
 */
class GridWidget extends WebWidget
{
    protected $query;

    protected $_columns = null;

    public function getColumnsNames()
    {
        if (null === $this->_columns) {
            if (!isset($this->config['query']['columns'])) {
                $this->config['query']['columns'] = array_keys($this->model->attributes());
            }
            foreach ($this->config['query']['columns'] as $col) {
                $this->_columns[$col] = $this->model->labelOf($col);
            }
        }
        return $this->_columns;
    }

    public function getRows()
    {
        $query = $this->model->select(array_keys($this->getColumnsNames()));
        if (isset($this->config['query']['orderby'])) {
            $query->orderBy($this->config['query']['orderby']);
        } else {
            $query->orderBy($this->model->getDefaultQueryOrdering());
        }
    }

    public function __construct(ModelInterface $model, $config = [])
    {
        $this->model = $model;
        $this->config = $config;
    }
}