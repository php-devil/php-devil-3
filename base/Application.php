<?php
namespace PhpDevil\framework\base;

class Application extends ApplicationPrototype  implements ApplicationInterface
{
    public function renderException(\Exception $e)
    {
        echo "\n" . $e->getMessage() . " (" . get_class($e) . ")<pre>";
            print_r($e);
        echo "</pre>";
    }

    public function __construct(array $config)
    {
        $this->setConfig($config);
    }
}