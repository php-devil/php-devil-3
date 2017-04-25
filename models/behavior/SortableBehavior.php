<?php
namespace PhpDevil\framework\models\behavior;
use PhpDevil\ORM\behavior\DefaultBehavior;
use PhpDevil\ORM\models\ActiveRecordInterface;
use PhpDevil\ORM\QueryBuilder\components\QueryCriteria;
use PhpDevil\ORM\QueryBuilder\components\QueryExpression;

/**
 * Class SortableBehavior
 * Таблицы с ручной сортировкой по целочисленному индексу (role = sort-index) в пределех
 * родительской записи (role = tree-parent) при ее наличии или со сквозным индексом по всей
 * таблице при отсутствии ссылки на родительскую запись
 * @package PhpDevil\framework\models\behavior
 */
class SortableBehavior extends DefaultBehavior
{
    /**
     * Имя типа поведения
     * @return string
     */
    public static function typeName()
    {
        return 'sortable';
    }

    /**
     * Имя класса набора поведений
     * @return string
     */
    public static function typeClass()
    {
        return 'table';
    }

    /**
     * Для NS дкревьев сортировка по умоляанию - возрастание левого ключа
     * @param $class
     * @return array
     */
    public static function defaultOrderBy($class)
    {
        $ordering = [];
        if ($parentLinkedField = $class::getRoleFieldStatic('tree-parent')) {
            $ordering[$parentLinkedField] = true;
        }
        $ordering[$class::getRoleFieldStatic('sort-index')] = true;
        return $ordering;
    }

    /**
     * Добавление ключей дерева к полям селект запроса
     * @param ActiveRecordInterface $class
     * @return array
     */
    public static function getSelectFields($class)
    {
        $select = [$class::getRoleFieldStatic('id'),];
        if ($parentLinkedField = $class::getRoleFieldStatic('tree-parent')) {
            $select[] = $parentLinkedField;
        }
        $select[] = $class::getRoleFieldStatic('sort-index');
        return $select;
    }

    /**
     * Подготовка записи перед вставкой
     * @param ActiveRecordInterface $row
     * @return bool
     */
    public static function beforeInsert(ActiveRecordInterface $row)
    {
        $row->setRoleValue('sort-index', static::getNextIndex($row));
        return true;
    }

    protected static function getNeighbourCondition(ActiveRecordInterface $row, $sign)
    {
        $conditions = [[$row->getRoleField('sort-index'), $sign, $row->getRoleValue('sort-index')]];
        if ($parentLinkField = $row->getRoleField('tree-parent')) {
            $conditions[] = [$parentLinkField, '=', $row->getRoleValue('tree-parent')];
        }
        return $conditions;
    }

    /**
     * @param ActiveRecordInterface $row
     * @return ActiveRecordInterface|null
     */
    protected static function findNextNeighbour(ActiveRecordInterface $row)
    {
        return $row::findOne(QueryCriteria::createAND(static::getNeighbourCondition($row, '>')), [$row->getRoleField('sort-index')=>true]);
    }

    /**
     * @param ActiveRecordInterface $row
     * @return ActiveRecordInterface|null
     */
    protected static function findPriorNeighbour(ActiveRecordInterface $row)
    {
        return $row::findOne(QueryCriteria::createAND(static::getNeighbourCondition($row, '<')), [$row->getRoleField('sort-index')=>false]);
    }

    public static function beforeUpdate(ActiveRecordInterface $row)
    {
        if ($oldValues = $row::findByPK($row->getRoleValue('id'))) {
            if ($oldValues->getRoleValue('tree-parent') != $row->getRoleValue('tree=parent')) {
                static::decrementNextNodes($row);
                $row->setRoleValue('sort-index', static::getNextIndex($row));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Рассчет индекса сортировки для новой записи
     * @param ActiveRecordInterface $row
     * @return int
     */
    protected static function getNextIndex(ActiveRecordInterface $row)
    {
        $where = null;
        if ($row->getRoleField('tree-parent')) {
            $where = QueryCriteria::createAND([[$row->getRoleField('tree-parent'), '=', $row->getRoleValue('tree-parent')]]);
        }
        $values = $row::query()->select(['max_sort_value' => QueryExpression::max($row->getRoleField('sort-index'))])
            ->where($where)->execute()->fetch();
        if (!isset($values['max_sort_value'])) $values['max_sort_value'] = 0;
        return intval($values['max_sort_value']) + 1;
    }

    /**
     * Декремент индекса сортировки последующих узлов
     * @param ActiveRecordInterface $row
     */
    protected static function decrementNextNodes(ActiveRecordInterface $row)
    {
        $old = $row::findByPK($row->getRoleValue('id'));
        if ($next = static::findNextNeighbour($old)) {
            $sortIndex = $row->getRoleField('sort-index');
            $skew = $next->getRoleValue('sort-index') - $old->getRoleValue('sort-index');
            $row::query()->update([
                $sortIndex => QueryExpression::math(['@' . $sortIndex, '-', $skew])
            ], QueryCriteria::createAND(static::getNeighbourCondition($old, '>')))
            ->execute();
        }
    }

    public static function moveLeft(ActiveRecordInterface $row)
    {
        if ($swap = static::findPriorNeighbour($row)) {
            static::swapRows($row, $swap);
        }
    }

    public static function moveRight(ActiveRecordInterface $row)
    {
        if ($swap = static::findNextNeighbour($row)) {
            static::swapRows($row, $swap);
        }
    }

    /**
     * Перестановка значений индекса сортировки между двумя записями
     * @param ActiveRecordInterface $row1
     * @param ActiveRecordInterface $row2
     */
    protected static function swapRows(ActiveRecordInterface $row1, ActiveRecordInterface $row2)
    {
        $id     = $row1->getRoleField('id');
        $column = $row1->getRoleField('sort-index');
        $value1 = $row1->getRoleValue('sort-index');
        $value2 = $row2->getRoleValue('sort-index');
        $row1::query()->update([$column=>$value2], QueryCriteria::createAND([[$id, '=', $row1->getRoleValue('id')]]))->execute();
        $row2::query()->update([$column=>$value1], QueryCriteria::createAND([[$id, '=', $row2->getRoleValue('id')]]))->execute();
    }
}