<?php
namespace PhpDevil\framework\console\commands;

class MigrateCommand extends AbstractConsoleCommand
{
    public function execute($params = null)
    {
        if (isset($params[0])) $connection = $params[0];
        else die("\nError: connection name is undefined");
        if (isset($params[1])) {
            if ('--hard' === $params[1]) {
                $this->migrateHard($params[0]);
            }
        }
    }

    private function migrateHard($connection)
    {
        echo "\nHARD MIGRATION MODE";
        echo "\nWill do down() for each applied migration and then up() for each migration";
        echo "\nAll data will be removed. Please, back up your database before continue";
        echo "\nContinue with database {$connection}? [Y/N]?\n>";
        $line = fgetc(STDIN);
        if ('y' == $line) {
            $migrations = \Devil::app()->db->getConnection($connection)->getMigrationManager();
            if ($applied = $migrations->getAppliedMigrations()) {
                // rollback all

            }

            echo 'done';
        }
    }
}