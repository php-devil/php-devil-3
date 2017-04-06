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
    protected $dataProvider;

    public function getRows()
    {
        return $this->dataProvider->all();
        
    }

    public function getColumnsNames()
    {
        $modelClass = $this->dataProvider->getPrototype();

        if (null === $this->_columns) {
            if (!isset($this->config['query']['columns'])) {
                $this->config['query']['columns'] = array_keys($modelClass::attributes());
            }
            foreach ($this->config['query']['columns'] as $col) {
                $this->_columns[$col] = $modelClass::labelOf($col);
            }
        }
        return $this->_columns;
    }

    public function __construct($dataProvider, $config)
    {
        $this->dataProvider  = $dataProvider;
        $this->config = $config;

        echo '<pre>';
        print_r($this);
    }
}