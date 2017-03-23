<?php
namespace PhpDevil\framework\base;
use PhpDevil\framework\common\Configurable;
use PhpDevil\framework\common\TagNamesTrait;

abstract class ControllerPrototype extends Configurable
{
    use TagNamesTrait;

    protected $owner = null;

    public function getOwner()
    {
        return $this->owner;
    }

    public function getNamespace()
    {
        $fullName = get_class($this);
        return substr($fullName, 0, strrpos($fullName, '\\'));
    }

    public function __construct($config = null, $owner = null)
    {
        $this->setConfig($config);
        $this->owner = $owner;
    }
}