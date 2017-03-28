<?php
namespace PhpDevil\framework\models;

use PhpDevil\framework\models\attributes\IntegerAttribut;
use PhpDevil\framework\models\attributes\PasswordAttribute;
use PhpDevil\framework\models\attributes\StringAttribute;
use PhpDevil\framework\models\helpers\Instantiator;

trait OrmModelsTrait
{
    /**
     * Типы атрибутов
     * @var array
     */
    protected static $extAttributeClassNames = [
        'integer'  => IntegerAttribut::class,
        'string'   => StringAttribute::class,
        'password' => PasswordAttribute::class,
    ];

    public static function getID()
    {

    }

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

    /**
     * Массив атрибутов модели из конфигурации
     * @return null
     */
    public static function attributes()
    {
        return Instantiator::helper()->getConfigured(static::class, 'attributes');
    }

    /**
     * Массив атрибутов модели из конфигурации
     * @return null
     */
    public static function table()
    {
        if ($table = Instantiator::helper()->getConfigured(static::class, 'table')) {
            if (!isset($table['connection'])) $table['connection'] = 'main';
            return $table;
        } else {
            return null;
        }
    }

    /**
     * Создание инстанса модели через хелпер
     * @return mixed
     */
    public static function model()
    {
        return Instantiator::helper()->load(static::class);
    }
}