<?php
namespace PhpDevil\framework\console\commands;


abstract class AbstractConsoleCommand
{
    abstract public function execute($params = null);
}