<?php
namespace PhpDevil\framework\components\db;
use PhpDevil\orm\connector\ExtendableConnector;

class Connector extends ExtendableConnector
{
    protected $config;

    protected $owner;

    public function getConnection($name)
    {
        if (!isset($this->connections[$name])) {
            $this->createConnection($name, $this->config[$name]);
        }
        return $this->connections[$name];
    }

    public function __construct($config, $owner = null)
    {
        $this->config = $config;
        $this->owner = $owner;
    }
}