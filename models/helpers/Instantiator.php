<?php
namespace PhpDevil\framework\models\helpers;


class Instantiator
{
    private static $instance = null;

    private $collection = [];

    public function load($className, $returnInstance = true)
    {
        if (!isset($this->collection[$className])) $this->collection[$className]['config'] = \Devil::loadConfig($className::config());
        if ($returnInstance) {
            if (!isset($this->collection[$className]['model'])) {
                $this->collection[$className]['model'] = new $className;
            }
            return clone($this->collection[$className]['model']);
        } else {
            return null;
        }
    }

    public function getConfigured($className, $paramName)
    {
        if (!isset($this->collection[$className])) $this->load($className, false);
        if (!isset($this->collection[$className])) die('Cant load ' . $className);
        if (isset($this->collection[$className]['config'][$paramName])) {
            return $this->collection[$className]['config'][$paramName];
        } else {
            return null;
        }
    }

    private function __construct()
    {
    }

    public static function helper()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}