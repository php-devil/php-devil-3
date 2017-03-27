<?php
namespace app\migrations\${connection};
use PhpDevil\framework\models\helpers\AbstractMigration;

/**
 * Миграция структуры БД.
 * создана: ${time}
 */
class ${classname} extends AbstractMigration
{
    /**
     * Время создания миграции
     * @var int
     */
    protected $mtime = ${time};

    /**
     * Имя соединения, для которого применяется миграция
     * @var string
     */
    protected $connection = '${connection}';

    /**
     * Выполнение миграции
     */
    public function up()
    {${up_body}
    }

    /**
     * Откат миграции
     */
    public function down()
    {${down_body}
    }
}
