<?php
namespace PhpDevil\framework\modules\migration\console;
use PhpDevil\framework\console\commands\AbstractConsoleCommand;

class HelpCommand extends AbstractConsoleCommand
{
    public function execute($params = null)
    {
        echo "\n" . 'PHPDevil migrate HELP version 1.0' . "\n";
        echo "\n" . 'create <module_tag>/<model_tag>    - creates migration with create|drop table queries';
        echo "\n" . 'create custom                      - creates empty migration';

        echo "\n\n";
    }
}