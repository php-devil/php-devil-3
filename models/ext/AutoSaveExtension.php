<?php
namespace PhpDevil\framework\models\ext;

trait AutoSaveExtension
{
    public function saveFromPost()
    {
        $id = str_replace('\\', '_', get_class($this));
        if ($data = \Devil::app()->post->getOnce($id)) {
            unset($data[$this->getRoleValue('id')]);
            $this->setAttributes($data);
            if ($this->validate()) {
                $this->save();
            } else {
                // validation contains errors
            }
        }
    }
}