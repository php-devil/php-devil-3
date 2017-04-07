<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\ext\AccessControlExtension;
use PhpDevil\framework\models\ext\AttributeTemplateExtension;
use PhpDevil\framework\models\ext\AttributeTypesExtension;
use PhpDevil\framework\models\ext\PropertyExtension;
use PhpDevil\ORM\models\ActiveRecord;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;

class StdTable extends ActiveRecord
{
    use AttributeTypesExtension; // - расширение типов данных ОРМ
    use AccessControlExtension;  // - расширение для контроля доступа к данным
    use PropertyExtension;
    use AttributeTemplateExtension;

    public static function db()
    {
        return \Devil::app()->db->getConnection((static::getConfig())['table']['connection']);
    }

    protected function createSortableBuffer(&$buffer)
    {
        $query = static::query()
            ->select([
                'parent' => $this->getRoleField('tree-parent'),
                'min_value' => QueryExpression::min($this->getRoleField('tree-left')),
                'max_value' => QueryExpression::max($this->getRoleField('tree-left'))
            ])->groupBy([$this->getRoleField('tree-parent')])->execute();

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