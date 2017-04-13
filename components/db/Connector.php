<?php
namespace PhpDevil\framework\components\db;
use PhpDevil\framework\models\relations\BelongsTo;
use PhpDevil\orm\connector\ExtendableConnector;
use PhpDevil\orm\generic\ConnectionInterface;

class Connector
{
    protected $config;

    protected $owner;

    /**
     * @param $name
     * @return mixed
     */
    public function getConnection($name)
    {
        return \PhpDevil\ORM\Connector::getInstance()->getConnection($name);
    }

    public function __construct($config, $owner = null)
    {
        $this->config = $config;
        $this->owner = $owner;
        foreach ($config as $name=>$conf) \PhpDevil\ORM\Connector::getInstance()->createConnection($name, $conf);
        \PhpDevil\ORM\Connector::getInstance()->setRelationClasses([
            'BelongsTo' => BelongsTo::class,
        ]);
    }
}