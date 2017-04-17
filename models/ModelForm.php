<?php
namespace PhpDevil\framework\models;
use PhpDevil\framework\models\ext\AccessControlExtension;
use PhpDevil\framework\models\ext\AttributeTypesExtension;
use PhpDevil\framework\models\ext\AutoSaveExtension;
use PhpDevil\framework\models\ext\PropertyExtension;
use PhpDevil\ORM\models\ActiveForm;

/**
 * Class StdForm
 * Расширение базовой формы ОРМ
 * @package PhpDevil\framework\models
 */
abstract class ModelForm extends ActiveForm
{
    use AttributeTypesExtension; // - расширение типов данных ОРМ
    use AccessControlExtension;  // - расширение для контроля доступа к данным
    use PropertyExtension;
    use AutoSaveExtension;

    protected $sourceModel = null;

    abstract public static function getModelClass();

    public function setSourceModel($model)
    {
        $class = static::getModelClass();
        if ($model instanceof  $class) {
            $this->sourceModel = $model;
            return true;
        } else {
            return false;
        }
    }

    public static function findByPK($value)
    {
        $class = static::getModelClass();
        $model = static::model();
        if ($source = $class::findByPK($value)) {
            $model->setSourceModel($source);
            return $model;
        } else {
            return null;
        }
    }

    public function save()
    {
        return $this->sourceModel->save();
    }
}