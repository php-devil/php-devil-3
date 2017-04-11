<?php
namespace PhpDevil\framework\modules\migration\models;
use PhpDevil\framework\modules\migration\components\MigrationInterface;
use PhpDevil\ORM\models\ActiveRecord;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;
use PhpDevil\ORM\QueryBuilder\queries\InsertQueryBuilder;
use PhpDevil\ORM\QueryBuilder\queries\SelectQueryBuilder;

/**
 * Class MigrationLog
 * Модель лога миграций.
 * Используется для каждого соединения с базами данных
 * @package PhpDevil\framework\modules\migration\models
 */
class MigrationLog
{
    protected $connection;

    /**
     * Добавление миграции в лог БД после выполнения
     * @param MigrationInterface $migration
     */
    public function completeUp(MigrationInterface $migration)
    {
        $query = (new InsertQueryBuilder())->into('phpdevil_migrations')->set([
            'id' => $migration->getTime()
        ]);

        $parsed = $query->parse($this->connection->getDialect());
        $this->connection->prepare($parsed->getSql())->execute($parsed->getArguments());
    }

    /**
     * Удаление миграции из лога выполненных после отката
     * @param MigrationInterface $migration
     */
    public function completeDown(MigrationInterface $migration)
    {
        echo "\n\n down done";
    }

    /**
     * Дата последней выполненной миграции
     * @return int
     */
    public function getLast()
    {
        try {
            $query = (new SelectQueryBuilder())->select([
                'max_value' => QueryExpression::max('id')
            ])->from('phpdevil_migrations');
            $sql = $query->parse($this->connection->getDialect())->getSQL();
            $data = $this->connection->prepare($sql)->execute()->fetch();
            return $data['max_value'];
        } catch (\PDOException $e) {
            return -1;
        }
    }

    public function __construct($connection)
    {
        $this->connection = \Devil::app()->db->getConnection($connection);
    }
}