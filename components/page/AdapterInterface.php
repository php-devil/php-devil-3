<?php
namespace PhpDevil\framework\components\page;

/**
 * Interface AdapterInterface
 * Требования к адаптерам шаблонных движков
 * @package PhpDevil\framework\components\page
 */
interface AdapterInterface
{
    /**
     * Присвоение переменной представлению
     * @param $name
     * @param $value
     */
    public function assignVar($name, $value);

    /**
     * Рендер представления с выводом
     * @param $view
     */
    public function display($view);

    /**
     * Рендер представления в строку
     * @param $view
     * @return string
     */
    public function fetch($view);
}