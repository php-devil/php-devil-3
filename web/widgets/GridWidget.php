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

    public function getMaxLevel()
    {
        if (isset($this->sortRanges['total']['max_level'])) {
            return $this->sortRanges['total']['max_level'];
        } else {
            return 0;
        }
    }

    public function getHeadColspan()
    {
        if (isset($this->config['manualSort']) && $this->config['manualSort']) {
            return 3;
        } else {
            return 1;
        }
    }

    public function appendRowControls($row)
    {
        if (isset($this->config['manualSort']) && $this->config['manualSort']) {
            $row->checkForManualSort($this->sortRanges, $this->dataProvider->getQuery()->getWhere());
        }
    }

    public function countControls()
    {
        if (isset($this->config['rowControls'])) {
            return count($this->config['rowControls']);
        } else {
            return 0;
        }
    }

    public function getCommonControls()
    {
        if (isset($this->config['gridControls'])) {
            foreach ($this->config['gridControls'] as $k=>$v) {
                if (isset($v['href'])) $this->config['gridControls'][$k]['href'] = $this->config['baseUrl'] . '/' . $v['href'];
            }
            return $this->config['gridControls'];
        } else {
            return null;
        }
    }

    public function getRowControls($row)
    {
        if (isset($this->config['rowControls'])) {
            $controls = $this->config['rowControls'];
            foreach ($controls as $k=>$v) {
                if ($row->accessControl($v['action'])) {
                    $controls[$k]['href'] = $this->config['baseUrl'] . '/'. $row->fromTemplate($v['href']);
                    $controls[$k]['isAllowed'] = true;
                } else {
                    $controls[$k]['isAllowed'] = false;
                }
            }
            return $controls;
        }
        return [];
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