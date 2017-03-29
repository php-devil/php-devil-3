<?php
namespace PhpDevil\framework\web;

class Module extends \PhpDevil\framework\base\Module
{
    /**
     * Проверка разрешения доступа на уровне модуля
     * @param $ruleName
     * @param null $param
     */
    public function accessControl($ruleName, $param = [])
    {
        if (isset($this->config['abac'][$ruleName])) {
            return call_user_func_array($this->config['abac'][$ruleName], $param);
        }
        return true;
    }
}