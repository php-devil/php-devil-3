<?php
namespace PhpDevil\framework\modules\migration\components\tables;

abstract class AbstractTable
{
    const MODE_CREATE = 1;
    const MODE_DROP = 2;

    const CONSTRAINT_DEFAULT  = 1;
    const CONSTRAINT_RESTRICT = 2;
    const CONSTRAINT_CASCADE  = 3;

    protected $connection;

    protected $tableName;

    protected $mode = 0;

    protected $sql = '';

    protected $_currentColumn = null;

    protected $_currentKey = null;

    protected $columns = [];

    protected $keys = [];

    abstract protected function createTable();

    abstract protected function dropTable();

    public function execute()
    {
        $this->saveColumn();
        $query = null;

        switch ($this->mode) {
            case self::MODE_CREATE:
                $query = $this->createTable();
                break;

            case self::MODE_DROP:
                $query = $this->dropTable();
                break;
        }

        if ($query) {
            try {
                $this->connection->prepare($query)->execute();
            } catch (\PDOException $e) {
                echo "\n\nPDO FATAL ERROR: " . $e->getCode() . "\n" . $e->getMessage() . "\n\n" . $query . "\n";
            }
        }
    }

    private function saveColumn()
    {
        if (!empty($this->_currentColumn)) {
            $this->columns[$this->_currentColumn['name']] = $this->_currentColumn['param'];
            $this->_currentColumn = null;
        }

        if (!empty($this->_currentKey)) {
            $this->keys[$this->_currentKey['name']] = $this->_currentKey['param'];
            $this->_currentKey = null;
        }
    }

    final public function column($name, $type)
    {
        if (!empty($this->_currentColumn)) $this->saveColumn();
        $this->_currentColumn['name'] = $name;
        $this->_currentColumn['param']['type'] = $type;
        $this->_currentColumn['param']['notnull'] = false;
        $this->_currentColumn['param']['default'] = null;
        return $this;
    }

    final public function notNull()
    {
        $this->_currentColumn['param']['notnull'] = true;
        return $this;
    }

    final public function defaultValue($value)
    {
        $this->_currentColumn['param']['default'] = $value;
        return $this;
    }

    final public function extra($value)
    {
        if (!isset($this->_currentColumn['param']['extra'])) $this->_currentColumn['param']['extra'] = [];
        $this->_currentColumn['param']['extra'][] = $value;
        return $this;
    }

    final public function key($name)
    {
        $this->saveColumn();
        $this->_currentKey = [
            'name'  => $name,
            'param' => [
                'type' => 'default',
            ],
        ];
        if ('primary' == $name) $this->withType('primary');
        return $this;
    }

    final public function withType($type)
    {
        $this->_currentKey['param']['type'] = $type;
        return $this;
    }

    final public function reference($table, $column)
    {
        $this->_currentKey['param']['ref_table'] = $table;
        $this->_currentKey['param']['ref_columns'] = $column;
        $this->constraint();
        return $this;
    }

    final public function constraint($delete = self::CONSTRAINT_DEFAULT, $update = self::CONSTRAINT_DEFAULT)
    {
        $this->_currentKey['param']['constraint'] = [
            'update' => $update,
            'delete' => $delete,
        ];
        return $this;
    }

    final public function onColls($colls)
    {
        $this->_currentKey['param']['columns'] = $colls;
        return $this;
    }

    public function __construct($connection, $name, $mode)
    {
        $this->connection = $connection;
        $this->tableName = $name;
        $this->mode = $mode;
    }
}