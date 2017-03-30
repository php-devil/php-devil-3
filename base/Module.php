<?php
namespace PhpDevil\framework\base;

use PhpDevil\framework\containers\Modules;

class Module extends ModulePrototype implements ModuleInterface
{
    /**
     * Конфигурация поведения модуля на фронтенде для метода run()
     * @return array
     */
    public static function frontend()
    {
        return [];
    }

    /**
     * Конфигурация модуля для бэкенд интерфейса
     * @return array
     */
    public static function backend()
    {
        return [];
    }

    /**
     * Проверка разрешений на выполнение модуля
     * @return bool
     */
    public function beforeRun()
    {
        return true;
    }

    /**
     * Выполняется после выполнения метода run модуля
     * только если beforeRun() вернул true
     */
    public function afterRun()
    {

    }

    /**
     * Выполняется вместо run() и afterRun()
     * если метод beforeRun() вернул false
     */
    public function errorRun()
    {

    }

    /**
     * Инстанс модуля.
     * Должен быть получен из контейнера модулей.
     * @return mixed
     */
    public static function module()
    {
        return Modules::container()->load(static::class);
    }
}