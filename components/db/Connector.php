<?php
namespace PhpDevil\framework\components\db;
use PhpDevil\orm\connector\ExtendableConnector;
use PhpDevil\orm\generic\ConnectionInterface;

class Connector extends ExtendableConnector
{
    protected $config;

    protected $owner;

    /**
     * @param $name
     * @return ConnectionInterface
     */
    public function getConnection($name)
    {
        if (!isset($this->connections[$name])) {
            $this->createConnection($name, $this->config[$name]);
        }
        return $this->connections[$name];
    }

    public function getSchema($name)
    {
        return $this->getConnection($name)->getSchema();
    }

    public function __construct($config, $owner = null)
    {
        $this->config = $config;
        $this->owner = $owner;
    }
}