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

    protected $sortRanges = null;

    public function appendRowControls($row)
    {
        if (isset($this->config['manualSort']) && $this->config['manualSort']) {
            $row->checkForManualSort($this->sortRanges, $this->dataProvider->getQuery()->getWhere());
        }
    }

    public function getRows()
    {

        return $this->dataProvider->all([$this, 'appendRowControls']);
    }

    public function getColumnsNames()
    {
        $modelClass = $this->dataProvider->getPrototype();

        if (null === $this->_columns) {
            if (!isset($this->config['columns'])) {
                $this->config['columns'] = array_keys($modelClass::attributes());
            }
            foreach ($this->config['columns'] as $col) {
                $this->_columns[$col] = $modelClass::labelOf($col);
            }
        }
        return $this->_columns;
    }

    public function __construct($dataProvider, $config)
    {
        $this->dataProvider  = $dataProvider;
        $this->config = $config;
    }
}