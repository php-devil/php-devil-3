<?php
namespace PhpDevil\framework\modules\migration\console;
use PhpDevil\framework\console\commands\AbstractConsoleCommand;
use PhpDevil\framework\modules\migration\models\MigrationLog;
use PhpDevil\framework\modules\migration\components\MigrationInterface;

/**
 * Class ApplyCommand
 * Реализация выполнения миграций
 * @package PhpDevil\framework\modules\migration\console
 */
abstract class ApplyCommand extends AbstractConsoleCommand
{
    /**
     * Менеджеры логирования миграций для каждого соединения с БД
     * @var array
     */
    protected $_managers = [];

    /**
     * Создание менеджера логирования транзакций при первом обращении
     * @param $connection
     * @return mixed
     */
    protected function createManager($connection)
    {
        if (!isset($this->_managers[$connection])) {
            $this->_managers[$connection] = new MigrationLog($connection);
        }
        return $this->_managers[$connection];
    }

    /**
     * Выполнение одной тракзации
     * @param MigrationInterface $migration
     */
    protected function migrateUp(MigrationInterface $migration)
    {
        echo "\nm_".$migration->getTime()." ... ";
        $migration->up();
        $this->createManager($migration->getConnection())->completeUp($migration);
        echo "OK";
    }

    /**
     * Выполнение всех транзакций для соединения
     * @param $name
     */
    protected function migrateConnectionUp($name)
    {
        $manager = $this->createManager($name);
        $lastApplied = $manager->getLast();
        echo "\n\nmigrate up $name from $lastApplied";
        $folder = \Devil::getPathOf('@app/migrations/' . $name);
        if (is_dir($folder)) {
            $d = opendir($folder);
            while ($e = readdir($d)) {
                if ('.' == $e || '..' == $e) continue;
                if (is_file($folder . '/' . $e)) {
                    $time = intval(substr($e, 2));
                    if ($time > $lastApplied) {
                        $className = '\\app\\migrations\\' . $name . '\\m_' . $time;
                        $this->migrateUp(new $className);
                    }
                }
            }
        }
        echo "\n--status:done";
    }

    /**
     * Выполнение всех миграций для всех соединений
     */
    protected function migrateAllUp()
    {

    }
}