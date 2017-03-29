<?php
namespace PhpDevil\framework\components\session;
use PhpDevil\framework\components\Component;

class Session extends Component implements SessionInterface
{
    private $currentArea = 'default';

    public function __get($name)
    {
        return $this->getValue($name);
    }

    public function __set($name, $value)
    {
        $this->setValue($name, $value);
    }

    /**
     * Получение значения из текущего подраздела сессии
     * @param $name
     * @return null
     */
    public function getValue($name)
    {
        if (isset($_SESSION[$this->currentArea][$name])) {
            return $_SESSION[$this->currentArea][$name];
        } else {
            return null;
        }
    }

    /**
     * Установка значения в заданном подразделе сессии
     * @param $name
     * @param $value
     * @return $this
     */
    public function setValue($name, $value)
    {
        $_SESSION[$this->currentArea][$name] = $value;
        return $this;
    }

    /**
     * Установка ключа верхнего уровня для хранения данных
     * используется для разделения сессии между разделами приложения
     * @param $name
     * @return $this
     */
    public function setArea($name)
    {
        $this->currentArea = $name;
        return $this;
    }

    /**
     * Предынициализация компонента
     */
    public function initAfterConstruct()
    {
        session_start();
        if (isset($this->config['area'])) {
            $this->setArea($this->config['area']);
        }
        parent::initAfterConstruct();
    }
}