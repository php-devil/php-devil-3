<?php
namespace PhpDevil\framework\models\ext;

/**
 * Class AccessControlExtension
 *
 * Расширение возможностей ОРМ для подключения атрибутивного контроля
 * доступа к данным на уровне модели/записи по имени конкретного действия. По умолчанию разрешены
 * все действия, если соответствующий метод не переопределен в досернем классе модели
 *
 * @package PhpDevil\framework\models\ext
 */
trait AccessControlExtension
{
    /**
     * Проверка доступа к действию
     * на уровне набора сущностей, представленных классом модели
     * @param $action
     * @return bool
     */
    public static function accessControlStatic($action)
    {
        return true;
    }

    /**
     * Проверка доступа к действию
     * на уровне загруженной записи
     * @param $action
     * @return bool
     */
    public function accessControl($action)
    {
        return true;
    }
}