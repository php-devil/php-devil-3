<?php
namespace PhpDevil\framework\models\ext;
use PhpDevil\framework\base\helpers\FilesHelper;
use PhpDevil\framework\helpers\MediaHelper;

/**
 * Class MediaExtension
 * Загрузка медиафайлов ActiveRecord
 * @package PhpDevil\framework\models\ext
 */
trait MediaExtension
{
    /**
     * Стек загруженных медиафайлов
     * @var array
     */
    protected $mediaStack = [];

    /**
     * Данные загруженного медиафайла из $_FILES
     * @param $name
     * @return array|null
     */
    protected function extractFromUploaded($name)
    {
        $modelID = str_replace('\\', '_', get_class($this));
        if (isset($_FILES[$modelID]['tmp_name'][$name]) && 0 == $_FILES[$modelID]['error'][$name]) {
            return [
                'name'     => $_FILES[$modelID]['name'][$name],
                'tmp_name' => $_FILES[$modelID]['tmp_name'][$name],
                'type'     => $_FILES[$modelID]['type'][$name],
                'size'     => $_FILES[$modelID]['size'][$name]
            ];
        } else {
            return null;
        }
    }

    public function clearAttached($name)
    {
        $config = static::getConfig();
        if (isset($config['media'][$name])) {
            $file = FilesHelper::createUploadableFile($config['media'][$name], $this->$name->getValue());
            $file->setPrimaryKey($this->getRoleValue('id'));
            $file->remove();
            $this->$name->setValue(null);
        }
    }

    public function beforeSave()
    {
        $config = static::getConfig();
        if (isset($config['media'])) foreach ($config['media'] as $attribute=>$options) {
            if ($up = $this->extractFromUploaded($attribute)) {
                $this->mediaStack[$attribute] = FilesHelper::createUploadableFile($options, $this->$attribute->getValue(), $up);
                if ($newName = $this->mediaStack[$attribute]->getNewFileName()) {
                    $this->$attribute->setValue($newName);
                }
            }
        }
    }

    public function afterSave()
    {
        if (!empty($this->mediaStack)) foreach($this->mediaStack as $k=>$v) {
            $v->setPrimaryKey($this->getRoleValue('id'))->upload();
        }
    }
}