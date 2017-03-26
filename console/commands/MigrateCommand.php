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
            echo "\n\nMIGRATION LOG:";
            $migrations = \Devil::app()->db->getConnection($connection)->getMigrationManager();
            if ($applied = $migrations->getAppliedMigrations()) {
                if (!empty($applied)) foreach($applied as $k=>$timestamp) {
                    $className = '\\app\\migrations\\' . $connection . '\\m_' . $timestamp;
                    echo "\n - " . $className;
                    (new $className)->down();
                    echo " ..OK";
                    $migrations->reportDownDone($timestamp);
                }
            }
            $dir = \Devil::getPathOf('@app/migrations/' . $connection);
            foreach(glob($dir . '/m_*.php') as $file) {
                $timestamp = intval(substr($file, strrpos($file, '/m_')+3));
                $className = '\\app\\migrations\\' . $connection . '\\m_' . $timestamp;
                echo "\n + " . $className;
                (new $className)->up();
                echo " ..OK";
                $migrations->reportUpDone($timestamp);
            }

            echo "\n\n\n done";
        }
    }
}