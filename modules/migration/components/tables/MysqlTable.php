<?php
namespace PhpDevil\framework\modules\migration\components\tables;

/**
 * Class MysqlTable
 * Формирование запросов CREATE|DROP|ALTER TABLE для MySQL соединений
 * @package PhpDevil\framework\modules\migration\components\tables
 */
class MysqlTable extends AbstractTable
{
    /**
     * Ограничения внешних ключей
     * @var array
     */
    protected $constraints = [
        self::CONSTRAINT_DEFAULT  => 'RESTRICT',
    //    self::CONSTRAINT_RESTRICT => 'RESTRICT',
    //    self::CONSTRAINT_CASCADE  => 'CASCADE',
    ];

    /**
     * Экранирование имен таблиц или полей
     * @param $what
     * @return string
     */
    protected function quote($what)
    {
        if (is_array($what)) {
            if (1 === count($what)) return '`' . $what[0] . '`';
            else return '`' . implode('`, `', $what) . '`';
        } else {
            return '`' . $what . '`';
        }
    }

    /**
     * Построение выражения для атрибута (поля) таблицы
     * @param $param
     * @return string
     */
    protected function buildAttribute($param)
    {
        if (0 === strpos($param['type'], 'string(')) $param['type'] = str_replace('string(', 'varchar(', $param['type']);
        $query = strtolower($param['type']);
        if ($param['notnull']) $query .= ' NOT NULL';
        if (isset($param['extra']) && !empty($param['extra'])) foreach ($param['extra'] as $e){
            $query .= ' ' . strtoupper($e);
        }
        return $query;
    }

    /**
     * Построение выражения для создания ключа
     * @param $name
     * @param $param
     * @return string
     */
    protected function buildKey($name, $param)
    {
        if ('primary' == $param['type']) {
            return 'PRIMARY KEY (' . $this->quote($param['columns']) . ')';
        } elseif ('foreign' == $param['type']) {
            return $this->buildForeignKey($name, $param);
        } else {
            if ('default' === $param['type']) {
                $param['type'] = 'INDEX';
            } else {
                $param['type'] = strtoupper($param['type']);
            }
            return $param['type'] . ' ' . $this->quote($name) . ' (' . $this->quote($param['columns']) . ')';
        }
    }

    /**
     * Построение выражения внешнего ключа
     * @param $name
     * @param $param
     * @return string
     */
    protected function buildForeignKey($name, $param)
    {
        $query = 'FOREIGN KEY ' . $this->quote($name) . '(' . $this->quote($param['columns']) . ')'
            . ' REFERENCES ' . $this->quote($param['ref_table']) . '(' . $this->quote($param['ref_columns']) . ')';
        if (isset($param['constraint'])) {
            $query .= ' ON DELETE ' . $this->constraints[$param['constraint']['delete']]
                . ' ON UPDATE ' . $this->constraints[$param['constraint']['update']];
        }
        return $query;
    }

    /**
     * Формирование запроса на создание таблицы
     * @param null $options
     * @return string
     */
    protected function createTable($options = null)
    {
        $query = 'CREATE TABLE ' . $this->quote($this->tableName) . '(';
        $delimiter = '';
        foreach ($this->columns as $k=>$v) {
            $query .= $delimiter . $this->quote($k) . ' ' . $this->buildAttribute($v);
            $delimiter = ', ';
        }
        foreach ($this->keys as $k=>$v) {
            $query .= $delimiter . $this->buildKey($k, $v);
            $delimiter = ', ';
        }
        if (!isset($options['engine']))  $options['engine']  = $this->connection->getDefaultEngine();
        if (!isset($options['charset'])) $options['charset'] = $this->connection->getDefaultCharset();
        $query .= ') ENGINE=' . $options['engine'] . ' DEFAULT CHARSET=' . $options['charset'];
        return $query;
    }

    /**
     * Формирование запроса на удаление таблицы
     * @return string
     */
    protected function dropTable()
    {
        return "drop table if exists `{$this->tableName}`";
    }
}