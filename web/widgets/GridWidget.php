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
    protected $columnsVisible = null;

    protected $inlineControlCount = null;

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
        return [];
    }

    public function getCommonControls()
    {
        
    }

    public function getColumnsNames()
    {
        $this->prepareVisibleColumns();
    }
}