<?php
namespace app\migrations\${connection};
use PhpDevil\framework\modules\migration\components\AbstractMigration;

/**
 * Миграция структуры БД.
 * создана: 0
 */
class m_0 extends AbstractMigration
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
    protected $connection = '${connection}';

    /**
     * Выполнение миграции
     */
    public function up()
    {
        $this->createTable('${connection}', 'phpdevil_migrations')
            ->column('id', 'int(10) unsigned')
            ->column('migration_date', 'timestamp')
            ->key('primary')->onColls('id')
            ->execute();
    }

    /**
     * Откат миграции
     */
    public function down()
    {
        $this->dropTable('${connection}', 'phpdevil_migrations')->execute();
    }
}
