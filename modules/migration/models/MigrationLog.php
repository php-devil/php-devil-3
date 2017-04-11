<?php
namespace PhpDevil\framework\modules\migration\models;
use PhpDevil\framework\modules\migration\components\MigrationInterface;
use PhpDevil\ORM\models\ActiveRecord;

/**
 * Class MigrationLog
 * Модель лога миграций.
 * Используется для каждого соединения с базами данных
 * @package PhpDevil\framework\modules\migration\models
 */
class MigrationLog extends ActiveRecord
{
    /**
     * Добавление миграции в лог БД после выполнения
     * @param MigrationInterface $migration
     */
    public function completeUp(MigrationInterface $migration)
    {

    }

    /**
     * Удаление миграции из лога выполненных после отката
     * @param MigrationInterface $migration
     */
    public function completeDown(MigrationInterface $migration)
    {

    }
}