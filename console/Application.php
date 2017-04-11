<?php
namespace PhpDevil\framework\console;

class Application extends \PhpDevil\framework\base\Application
{
    public function execute($commandName, $params = [])
    {
        \Devil::registerApplication($this);
        $module = null;
        if ($module = $this->loadModule($commandName)) {
            $commandName = array_shift($params);
            $module->execute($commandName, $params);
        } elseif (false !== ($slash = strpos($commandName, '/'))) {
            $moduleName = substr($commandName, 0, $slash);
            if ($module = $this->loadModule($moduleName)) {
                $module->execute(substr($commandName, $slash + 1), $params);
            } else {
                die ("\n module " . $moduleName . ' not found');
            }
        } else {
            parent::execute($commandName, $params);
        }
    }
}