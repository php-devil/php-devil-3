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
     * Комментарий к миграции для логирования
     * @return string
     */
    public function comment()
    {
        return 'Создание таблицы лога миграций';
    }

    /**
     * Выполнение миграции
     */
    public function up()
    {
        $this->createTable('phpdevil_migrations')
            ->column('id', 'char(14)')
            ->column('migration_date', 'timestamp')
            ->column('comment', 'string(255)')
            ->key('primary')->onColls('id')
            ->execute();
    }

    /**
     * Откат миграции
     */
    public function down()
    {
        $this->dropTable('phpdevil_migrations')->execute();
    }
}
