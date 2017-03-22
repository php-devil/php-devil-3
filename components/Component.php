<?php
namespace PhpDevil\framework\components;
use PhpDevil\framework\common\Configurable;

abstract class Component extends Configurable
{
    /**
     * Владелец компонента (приложение или модуль)
     * @var null
     */
    protected $owner = null;

    /**
     * Флаг вызова initOnce
     * @var bool
     */
    protected $initDone = false;

    /**
     * Инициализация компонента, выполняется один раз (запускает метод init).
     * При последующих вызовах игнорируется
     */
    final public function initOnce()
    {
        if (false === $this->initDone) {
            $this->init();
            $this->initDone = true;
        }
    }

    /**
     * Инициализация компонента
     */
    protected function init()
    {
        // по умолчанию ничего не делаем
    }

    /**
     * Действия, которые нужно выполнить после создания инстанса
     */
    protected function initAfterConstruct()
    {
        // по умолчанию ничего не делаем
    }

    /**
     * Component constructor.
     * @param $config
     * @param null $owner
     */
    final public function __construct($config, $owner = null)
    {
        $this->setConfig($config);
        $this->owner = $owner;
        $this->initAfterConstruct();
    }
}