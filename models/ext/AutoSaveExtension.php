<?php
namespace PhpDevil\framework\models\ext;

trait AutoSaveExtension
{
    /**
     * Валидация и автосохранение данных из $_POST
     * @return bool
     */
    public function saveFromPost()
    {
        $id = str_replace('\\', '_', get_class($this));
        if ($data = \Devil::app()->post->getOnce($id)) {
            unset($data[$this->getRoleValue('id')]);
            $this->setAttributes($data);
            if ($this->validate()) {
                $this->save();
                return true;
            } else {
                // validation contains errors
            }
        }
        return false;
    }

    /**
     * Автозпролнение атрибутов модели из $_POST без попытки
     * валидации и сохранения
     * @return bool
     */
    public function fillFromPost()
    {
        $id = str_replace('\\', '_', get_class($this));
        if ($data = \Devil::app()->post->getOnce($id)) {
            unset($data[$this->getRoleValue('id')]);
            $this->setAttributes($data);
            return true;
        }
        return false;
    }
}