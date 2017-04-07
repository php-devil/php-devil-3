<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\ext\AccessControlExtension;
use PhpDevil\framework\models\ext\AttributeTemplateExtension;
use PhpDevil\framework\models\ext\AttributeTypesExtension;
use PhpDevil\framework\models\ext\PropertyExtension;
use PhpDevil\ORM\models\ActiveForm;

/**
 * Class StdForm
 * Расширение базовой формы ОРМ
 * @package PhpDevil\framework\models
 */
class StdForm extends ActiveForm
{
    use AttributeTypesExtension; // - расширение типов данных ОРМ
    use AccessControlExtension;  // - расширение для контроля доступа к данным
    use PropertyExtension;
    use AttributeTemplateExtension;
}