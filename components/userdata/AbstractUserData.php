<?php
namespace PhpDevil\framework\components\userdata;
use PhpDevil\framework\components\Component;

abstract class AbstractUserData extends Component
{
    protected $source = [];

    protected $onceDone = [];

    public function __get($name)
    {
        if (isset($this->source[$name])) {
            return $this->source[$name];
        } else {
            return null;
        }
    }

    public function getOnce($name)
    {
        if (isset($this->source[$name]) && !isset($this->onceDone[$name])) {
            $this->onceDone[$name] = true;
            return $this->source[$name];
        } else {
            return null;
        }
    }

}