<?php
namespace PhpDevil\framework\modules\migration\components;

use PhpDevil\framework\modules\migration\components\tables\AbstractTable;
use PhpDevil\framework\modules\migration\components\tables\MysqlTable;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * Время создания миграции
     * @var int
     */
    protected $mtime = 0;

    /**
     * Имя соединения, для которого применяется миграция
     * @var string
     */
    protected $connection = 'main';

    public function comment()
    {
        return '';
    }

    public function getTime()
    {
        return $this->mtime;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected $tables = [
        'mysql' => MysqlTable::class,
    ];

    protected function createTable($name)
    {
        $connection = \Devil::app()->db->getConnection($this->connection);
        $class = $this->tables[$connection->getDialect()];
        return new $class($connection, $name, AbstractTable::MODE_CREATE);
    }

    protected function dropTable($name)
    {
        $connection = \Devil::app()->db->getConnection($this->connection);
        $class = $this->tables[$connection->getDialect()];
        return new $class($connection, $name, AbstractTable::MODE_DROP);
    }
}