<?php
namespace PhpDevil\framework\modules\migration\components;

/**
 * Class TemporaryMigration
 * Временная миграция (до записи в файл)
 * @package PhpDevil\framework\modules\migration\components
 */
class TemporaryMigration
{
    /**
     * Класс модели
     * @var string
     */
    protected $keyClass;

    /**
     * Имя соединения с БД
     * @var string
     */
    protected $connection;

    /**
     * Имя таблицы БД
     * @var string
     */
    protected $tableName;

    /**
     * Поля таблицы
     * @var array
     */
    protected $columns = [];

    /**
     * Ключи таблицы
     * @var array
     */
    protected $keys = [];

    protected $extras = '';

    public function getConnection()
    {
        return $this->connection;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getModelClass()
    {
        return $this->keyClass;
    }

    public function getCreateMigration()
    {
        $sql = "\n\t\t" . '$this->createTable(' . "'{$this->tableName}'" . ')';
        foreach ($this->columns as $column) $sql .=  "\n\t\t\t" . $column;
        foreach ($this->keys as $column)    $sql .=  "\n\t\t\t" . $column;
        $sql .= "\n\t\t\t" . $this->extras . '->execute();';
        return $sql;
    }

    public function getDropMigration()
    {
        return "\n\t\t" . '$this->dropTable(' . "'{$this->tableName}'" . ')->execute();';
    }

    protected function createKeys($arr)
    {
        foreach ($arr as $name=>$config) {
            $raw = "->key('{$name}')";
            if (isset($config['type'])) {
                if ('foreign' === $config['type'] || 'self' !== $config['model']) {
                    Dependencies::push($config['model']);
                    // todo: ->reference()->constr()
                }
                $raw .= "->withType('{$config['type']}')";
                unset($config['type']);
            }

            if (isset($config['columns'])) {
                $columns = $config['columns'];
                unset($config['columns']);
            } else {
                $columns = $config;
            }

            if (is_array($columns)) {
                $raw .= "->onColls(['" . implode("', '", $columns) . "'])";
            } else {
                $raw .= "->onColls('{$columns}')";
            }

            $this->keys[$name] = $raw;
        }
    }

    protected function createColumns($attributes)
    {
        foreach ($attributes as $name=>$config) {
            $query = "->column('{$name}', '{$config['type']}')";
            if (!isset($config['nullable']) || $config['nullable']) $query .= ''; else $query .= '->notNull()';
            if (isset($config['default'])) $query .= "->defaultValue('{$config['default']}'')";
            if (isset($config['extra'])) $query .= "->extra('{$config['extra']}')";
            $this->columns[$name] = $query;
        }
    }

    public function __construct($modelClassName)
    {
        $this->keyClass = $modelClassName;
        $config = $modelClassName::getConfig();
        if (isset($config['table'])) {
            $this->connection = $config['table']['connection'];
            $this->tableName  = $config['table']['name'];
            if (isset($config['table']['keys'])) {
                $this->createKeys($config['table']['keys']);
            }
            $this->createColumns($config['attributes']);
        } else {
            Dependencies::done($modelClassName);
        }
    }
}