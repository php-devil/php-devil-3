<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\web\WebWidget;
use PhpDevil\framework\models\ModelInterface;
use PhpDevil\ORM\models\ActiveRecordInterface;

/**
 * Class GridWidget
 * Табличное представление данных
 * @package PhpDevil\framework\web\widgets
 */
class GridWidget extends WebWidget
{
    protected $columnsVisible = null;

    protected $inlineControlCount = null;

    public function isManualSortable()
    {
        return $this->config['manualSort'];
    }

    protected function prepareVisibleColumns()
    {
        if (null === $this->columnsVisible) {
            $prototype = get_class($this->provider->getPrototype());
            if (!isset($this->config['columns'])) {
                $this->config['columns'] = $prototype::attributes();
            }
            foreach ($this->config['columns'] as $col) {
                $this->columnsVisible[$col] = $prototype::labelOf($col);
            }
        }
    }

    public function attribute(ActiveRecordInterface $row, $alias)
    {
        return $row->$alias->getValue();
    }

    public function countControls()
    {
        if (null === $this->inlineControlCount) {
            if (!isset($this->config['rowControls'])) $this->inlineControlCount = 0;
            else $this->inlineControlCount = count($this->config['rowControls']);
        }
        return $this->inlineControlCount;
    }

    public function getRows()
    {
        return $this->provider->all();
    }

    public function getCommonControls()
    {

    }

    public function getColumnsNames()
    {
        $this->prepareVisibleColumns();
        return $this->columnsVisible;
    }

    public function getAttributes()
    {
        $this->prepareVisibleColumns();
        return array_keys($this->columnsVisible);
    }
}