<?php
namespace PhpDevil\framework\models\ext;

use PhpDevil\framework\models\attributes\DatetimeAttribute;
use PhpDevil\framework\models\attributes\IntegerAttribute;
use PhpDevil\framework\models\attributes\PasswordAttribute;
use PhpDevil\framework\models\attributes\StringAttribute;

trait AttributeTypesExtension
{
    /**
     * Классы, расширяющие типы атрибутов из ОРМ
     * @var array
     */
    protected static $_extendedAttributes = [
        'integer' => IntegerAttribute::class,

        'string'   => StringAttribute::class,
        'password' => PasswordAttribute::class,

        'datetime'  => DatetimeAttribute::class,
        'timestamp' => DatetimeAttribute::class,
    ];

    public static function getAttributeClass($type)
    {
        if (isset(self::$_extendedAttributes[$type])) {
            return self::$_extendedAttributes[$type];
        } else {
            echo " [[[$type]]] ";
            return parent::getAttributeClass($type);
        }
    }
}