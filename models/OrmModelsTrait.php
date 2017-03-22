<?php
namespace PhpDevil\framework\models;

use PhpDevil\framework\models\attributes\PasswordAttribute;

trait OrmModelsTrait
{
    /**
     * Типы атрибутов
     * @var array
     */
    protected static $extAttributeClassNames = [
        'password' => PasswordAttribute::class,
    ];

    /**
     * Получение класса атрибута по его типу с учетом расширения типов
     * @param $type
     * @return mixed
     */
    public static function getAttributeClass($type)
    {
        if (isset(static::$extAttributeClassNames[$type])) {
            return static::$extAttributeClassNames[$type];
        } else {
            return parent::getAttributeClass($type);
        }
    }
}