<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\common\TagNamesTrait;
use PhpDevil\orm\models\ActiveForm;

abstract class Form extends ActiveForm implements ModelInterface
{
    use OrmModelsTrait;
    use TagNamesTrait;
}