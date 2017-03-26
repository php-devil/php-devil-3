<?php
namespace PhpDevil\framework\models\helpers;


class AbstractMigration
{
    protected $mtime = 0;

    protected $connection = 'main';

    protected static $appliedMigrations = null;

    const UNSIGNED = 1;

    const NOT_NULL = 2;

    const AUTO_INCREMENT = 3;

    public function schema()
    {
        return \Devil::app()->db->getConnection($main)->getSchema();
    }

    public function apply($time = 0)
    {
        if (0 === time) {

        }

    }


}