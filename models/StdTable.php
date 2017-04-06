<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\ext\AccessControlExtension;
use PhpDevil\framework\models\ext\AttributeTypesExtension;
use PhpDevil\ORM\models\ActiveRecord;

class StdTable extends ActiveRecord
{
    use AttributeTypesExtension; // - расширение типов данных ОРМ
    use AccessControlExtension;  // - расширение для контроля доступа к данным
}