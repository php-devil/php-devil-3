<?php
namespace PhpDevil\framework\models\helpers;


class AbstractMigration
{
    protected $mtime = 0;

    protected $connection = 'main';

    public function exec($sql, $argv = null)
    {
        return \Devil::app()->db->getConnection($this->connection)
            ->prepare($sql)->execute($argv);
    }


}