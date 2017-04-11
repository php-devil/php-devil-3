<?php
namespace PhpDevil\framework\modules\migration\components\tables;

class MysqlTable extends AbstractTable
{
    protected function createTable($options = null)
    {
        $query = "create table `{$this->tableName}` (";
        $delimiter = '';
        foreach($this->columns as $k=>$v) {
            if (0 === strpos($v['type'], 'string(')) $v['type'] = str_replace('string(', 'varchar(', $v['type']);
            $attr = "`{$k}` {$v['type']}";
            if ($v['notnull']) $attr .= " NOT NULL";
            if (isset($v['extra']) && !empty($v['extra'])) foreach ($v['extra'] as $e) $attr .= ' ' . $e;
            $query .= $delimiter . $attr;
            $delimiter = ', ';
        }
        foreach ($this->keys as $k=>$v)
        {
            switch ($v['type']) {

                default:
                    if ('primary' === $v['type']) $v['type'] = 'PRIMARY KEY';
                    if ('default' === $v['type']) $v['type'] = 'INDEX';
                    $query .= $delimiter . strtoupper($v['type']) . " `{$k}` (`";
                    if (is_array($v['columns'])) $query .= implode('`, `', $v['columns']);
                    else $query .= $v['columns'];
                    $query .= "`)";
            }
        }
        if (!isset($options['engine']))  $options['engine']  = $this->connection->getDefaultEngine();
        if (!isset($options['charset'])) $options['charset'] = $this->connection->getDefaultCharset();
        $query .= ') ENGINE=' . $options['engine'] . ' DEFAULT CHARSET=' . $options['charset'];
        return $query;
    }

    protected function dropTable()
    {
        return "drop table if exists `{$this->tableName}`";
    }
}