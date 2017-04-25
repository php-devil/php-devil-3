<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\behavior\SortableBehavior;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;

class SortableTable extends StdTable
{
    /**
     * Определение поведения модели в целом
     * индекс ручной сортировки записей
     * @return mixed
     */
    public static function mainBehavior()
    {
        return SortableBehavior::class;
    }

    /**
     * Подготовка буфера для вычисления индекса сортировок
     * @param $buffer
     */
    protected function createSortableBuffer(&$buffer)
    {
        $query = static::query()
            ->select([
                'parent' => $this->getRoleField('tree-parent'),
                'max_value' => QueryExpression::max($this->getRoleField('sort-index'))
            ])->groupBy([
                $this->getRoleField('tree-parent'),
            ])->execute();
        while ($row = $query->fetch()) {
            $buffer['rows'][$row['parent']] = $row['max_value'];
        }
    }

    /**
     * Проверка возможности ручной сортировки
     * @param $buffer
     */
    public function checkForManualSort(&$buffer)
    {
        if (null === $buffer) $this->createSortableBuffer($buffer);
        $allowUp = (bool) ($this->getRoleValue('sort-index') > 1);
        $allowDown = (bool) ($this->getRoleValue('sort-index') < $buffer['rows'][$this->getRoleValue('tree-parent')]);
        $this->setProperty('sort_up', $allowUp)->setProperty('sort_down', $allowDown);
    }
}