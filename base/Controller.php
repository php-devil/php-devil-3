<?php
namespace PhpDevil\framework\base;
use PhpDevil\framework\web\http\HttpException;

abstract class Controller extends ControllerPrototype implements ControllerInterface
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

    public function loadModel($tagName)
    {
        return $this->getOwner()->loadModel($tagName);
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

    final protected function runActionClass($class, $actionName, $param = [])
    {
        if ($this->beforeAction($actionName)) {
            $param['controller'] = $this;
            call_user_func_array([$class, 'run'], $param);
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
        } else {
            $actionClass = substr(get_class($this), 0, strrpos(get_class($this), '\\')+1) . $this->getTagName() . '\\';
            $actionClass .= $actionName . 'Action';
            if (class_exists($actionClass)) {
                $this->runActionClass($actionClass, $actionName);
            } else {
                if (false !== strpos($actionName, '.')) {
                    list($model, $modelAction) = explode('.', $actionName);
                    $tryMethod = ucfirst($model) . ucfirst($modelAction);
                    if (method_exists($this, 'action' . $tryMethod)) {
                        $this->runActionMethod($tryMethod);
                    } elseif (method_exists($this, $modelAction)) {
                        if ($this->beforeAction($tryMethod)) {
                            $this->$modelAction($this->loadModel($model), $param);
                            $this->afterAction($tryMethod);
                        } else {
                            $this->errorAction($tryMethod);
                        }
                    } else {
                        throw new HttpException(HttpException::NOT_FOUND);
                    }
                }
            }
        }
    }
}