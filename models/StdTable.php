<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\ext\AccessControlExtension;
use PhpDevil\framework\models\ext\AttributeTypesExtension;
use PhpDevil\ORM\models\ActiveRecord;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;

class StdTable extends ActiveRecord
{
    use AttributeTypesExtension; // - расширение типов данных ОРМ
    use AccessControlExtension;  // - расширение для контроля доступа к данным

    public static function db()
    {
        return \Devil::app()->db->getConnection((static::getConfig())['table']['connection']);
    }

    protected function createSortableBuffer(&$buffer)
    {
        $query = static::query()->select()->execute();

        while ($row = $query->fetch()) {
            print_r($row);
        }
        die;
    }

    public function checkForManualSort(&$buffer)
    {
        if (null === $buffer) $this->createSortableBuffer($buffer);
    }

    /**
     * todo: убрать в виджет
     * @return int
     */
    public function getLevel()
    {
        return 0;
    }
}