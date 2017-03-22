<?php
namespace PhpDevil\framework\base;


abstract class Controller extends ControllerPrototype
{
    abstract public function render($view, $attributes = []);

    public function beforeAction($actionName = null)
    {
        return true;
    }

    public function afterAction($actionName = null)
    {

    }

    public function errorAction($actionName = null)
    {

    }

    final protected function runActionMethod($actionName, $param = [], $realMethod = null)
    {
        if (null === $realMethod) $realMethod = 'action' . $actionName;
        if ($this->beforeAction($actionName)) {
            call_user_func_array([$this, $realMethod], $param);
            $this->afterAction($actionName);
        } else {
            $this->errorAction($actionName);
        }
    }

    public function performAction($actionName, $param = [])
    {
        $methodName = 'action' . $actionName;
        if (method_exists($this, $methodName)) {
            $this->runActionMethod($actionName, $param);
        }
    }
}