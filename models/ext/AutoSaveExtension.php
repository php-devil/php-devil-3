<?php
namespace PhpDevil\framework\models\ext;

trait AutoSaveExtension
{
    public function saveFromPost()
    {
        $id = str_replace('\\', '_', get_class($this));
        if (isset($_POST[$id])) {
            unset($_POST[$id][$this->getRoleValue('id')]);
            $this->setAttributes($_POST[$id]);
            if ($this->validate()) {
                // validation OK, saving model
            } else {
                // validation contains errors
            }
        }
    }
}