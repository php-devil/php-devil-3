<?php
namespace PhpDevil\framework\base;

use PhpDevil\framework\containers\Modules;

abstract class ApplicationPrototype extends ModulePrototype implements ApplicationInterface
{
    public function loadModule($tagName)
    {
        return Modules::container()->load($tagName);
    }
}