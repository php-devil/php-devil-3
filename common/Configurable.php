<?php
namespace PhpDevil\framework\common;

class Configurable
{
    protected $config = null;

    protected $virtualLocation = null;

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
    
    /**
     * Определение директории, в которой расположен файл класса
     * @return string
     */
    public static function getLocationStatic()
    {
        return dirname((new \ReflectionClass(static::class))->getFileName());
    }

    /**
     * Определение лиректории в которой расположен файл класса
     * или располагался бы (если вмртуальный класс задан прототипом)
     * @return null|string
     */
    public function getLocation()
    {
        if ($this->virtualLocation) {
            return $this->virtualLocation;
        } else {
            return dirname((new \ReflectionClass(static::class))->getFileName());
        }
    }
}