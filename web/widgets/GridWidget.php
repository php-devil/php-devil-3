<?php
namespace PhpDevil\framework\web\widgets;
use PhpDevil\framework\web\WebWidget;
use PhpDevil\framework\models\ModelInterface;
use PhpDevil\ORM\models\ActiveRecordInterface;
use PhpDevil\ORM\providers\DataProviderInterface;

/**
 * Class GridWidget
 * Табличное представление данных
 * @package PhpDevil\framework\web\widgets
 */
class GridWidget extends WebWidget
{
    protected $columnsVisible = null;

    protected $inlineControlCount = null;

    protected $sortables = null;


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

    public function getCommonControls()
    {
        $ctrl = [];
        if (isset($this->config['gridControls'])) foreach($this->config['gridControls'] as $c){
            if (isset($c['href'])) $c['href'] = $this->config['baseUrl'] . '/' . $c['href'];

            $ctrl[] = $c;
        }
        return $ctrl;
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

    public function checkManualSort(ActiveRecordInterface $row)
    {
        $row->checkForManualSort($this->sortables);
    }

    public function getRows()
    {
        $callback = null;
        if ($this->isManualSortable()) $callback = [$this, 'checkManualSort'];
        return $this->provider->all($callback);
    }

    public function getRowControls(ActiveRecordInterface $row)
    {
        $controls = [];
        if (isset($this->config['rowControls'])) foreach($this->config['rowControls'] as $c) {
            $c['href'] = $this->config['baseUrl'] . '/' . $row->fromTemplate($c['href']);
            $c['isAllowed'] = $row->accessControl($c['action']);
            $controls[] = $c;
        }
        return $controls;
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

    final public function __construct(DataProviderInterface $provider, $config)
    {
        $this->provider = $provider;
        $this->config = $config;
    }
}