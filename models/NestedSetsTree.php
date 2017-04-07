<?php
namespace PhpDevil\framework\models;
use PhpDevil\ORM\behavior\NestedSets;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;

class NestedSetsTree extends StdTable
{
    /**
     * Определение поведения модели в целом
     * (дерево NS, списки с сортировкой по полю, с русной сортировкой по полю, маппер и т.п.)
     * @return mixed
     */
    public static function mainBehavior()
    {
        return NestedSets::class;
    }

    public function getLevel()
    {
        return intval($this->getRoleValue('tree-level'));
    }

    protected function createSortableBuffer(&$buffer)
    {
        $maxLevel = 0;
        $query = static::query()
            ->select([
                'parent' => $this->getRoleField('tree-parent'),
                'level'  => $this->getRoleField('tree-level'),
                'min_value' => QueryExpression::min($this->getRoleField('tree-left')),
                'max_value' => QueryExpression::max($this->getRoleField('tree-left'))
            ])->groupBy([
                $this->getRoleField('tree-parent'),
                $this->getRoleField('tree-level'),
            ])->execute();
        while ($row = $query->fetch()) {
            if ($row['level'] > $maxLevel) $maxLevel = $row['level'];
            $buffer['rows'][$row['parent']] = ['min' => $row['min_value'], 'max' => $row['max_value']];
        }
        $buffer['total']['max_level'] = $maxLevel;
    }

    public function checkForManualSort(&$buffer)
    {
        if (null === $buffer) $this->createSortableBuffer($buffer);
        $allowUp = (bool) ($this->getRoleValue('tree-left') > $buffer['rows'][$this->getRoleValue('tree-parent')]['min']);
        $allowDown = (bool) ($this->getRoleValue('tree-left') < $buffer['rows'][$this->getRoleValue('tree-parent')]['max']);
        $this->setProperty('sort_up', $allowUp)->setProperty('sort_down', $allowDown);
    }
}