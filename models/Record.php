<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\common\TagNamesTrait;
use PhpDevil\orm\models\ActiveRecord;

abstract class Record extends ActiveRecord implements ModelInterface
{
    use OrmModelsTrait;
    use TagNamesTrait;

}