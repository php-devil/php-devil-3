<?php
namespace PhpDevil\framework\modules\migration\console;
use PhpDevil\framework\modules\migration\components\MigrationInterface;

class UpCommand extends ApplyCommand
{
    public function execute($params = null)
    {
        if (isset($params[0])) {
            $this->migrateConnectionUp($params[0]);
        } else {
            $this->migrateAllUp();
        }
    }
}