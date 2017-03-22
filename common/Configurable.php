<?php
namespace PhpDevil\framework\common;

class Configurable
{
    protected $config = null;

    /**
     * Назначение массива конфигурации объекту
     * @param array|null $config
     * @return $this
     */
    public function setConfig(array $config = null)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Получение подмассива конфигурации либо конфигурации в целом
     * @param null $keyName
     * @return null
     */
    public function getConfig($keyName = null)
    {
        if (null === $keyName) {
            return $this->config;
        } elseif (isset($this->config[$keyName])) {
            return $this->config[$keyName];
        } else {
            return null;
        }
    }
}