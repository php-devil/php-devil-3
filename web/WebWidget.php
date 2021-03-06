<?php
namespace PhpDevil\framework\web;
use PhpDevil\ORM\providers\DataProviderInterface;

/**
 * Class WebWidget
 * Виждет веб приложения
 * @package PhpDevil\framework\web
 */
abstract class WebWidget
{
    /**
     * Начальная конфигурация виджета
     * @var array
     */
    protected $config = [];

    /**
     * Провайдер данных
     * @var DataProviderInterface
     */
    protected $provider;

    public function getHint($name)
    {
        if (isset($this->config['hints'][$name])) {
            return $this->config['hints'][$name];
        } else {
            return null;
        }
    }
}