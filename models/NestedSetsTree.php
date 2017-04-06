<?php
namespace PhpDevil\framework\models;
use PhpDevil\ORM\behavior\NestedSets;

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
}