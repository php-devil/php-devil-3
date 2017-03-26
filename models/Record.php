<?php
namespace PhpDevil\framework\models;
use PhpDevil\orm\models\ActiveRecord;

class Record extends ActiveRecord
{
    use OrmModelsTrait;
    use TagNamesTrait;

    public static function db()
    {

    }
}