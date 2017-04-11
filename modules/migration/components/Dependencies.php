<?php
namespace PhpDevil\framework\modules\migration\components;

/**
 * Class Dependencies
 * Стек зависимостей для построения миграций
 * @package PhpDevil\framework\modules\migration\components
 */
class Dependencies
{
    private static $instance = null;

    protected $_stack = [];

    protected $_done = [];

    protected $_real = [];

    protected function addModel($modelClassName)
    {
        if (!isset($this->_stack[$modelClassName]) && !isset($this->_done[$modelClassName])) {
            $temp = new TemporaryMigration($modelClassName);
            $this->_stack[$modelClassName] = $temp;
        }
    }

    protected function saveAll()
    {
        foreach ($this->_stack as $m) {
            $conn = $m->getConnection();
            if (!isset($this->_real[$conn])) $this->_real[$conn] = ['up' => [], 'down' => []];
            $this->_real[$conn]['up'][]   = $m->getCreateMigration();
            $this->_real[$conn]['down'][] = $m->getDropMigration();
            $this->reportDone($m->getModelClass());
        }
        return $this->_real;
    }

    protected function reportDone($modelClassName)
    {
        unset($this->_stack[$modelClassName]);
        $this->_done[$modelClassName] = true;
    }

    public static function push($modelClassName)
    {
        static::getInstance()->addModel($modelClassName);
    }

    public static function flush()
    {
        return static::getInstance()->saveAll();
    }

    public static function done($modelClassName)
    {
        static::getInstance()->reportDone($modelClassName);
    }

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }
}