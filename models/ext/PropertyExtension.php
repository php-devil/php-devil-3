<?php
namespace PhpDevil\framework\models\ext;

trait PropertyExtension
{
    protected $_properties = [];

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }

    public function getProperty($name)
    {
        if ($this->hasProperty($name)) {
            return $this->_properties[$name];
        } else {
            return null;
        }
    }

    public function hasProperty($name)
    {
        return isset($this->_properties[$name]);
    }

}