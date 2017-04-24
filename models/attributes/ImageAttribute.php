<?php
namespace PhpDevil\framework\models\attributes;

class ImageAttribute extends FileAttribute
{
    public function getHtmlType()
    {
        return 'image';
    }

    public function getRemoveName()
    {
        return str_replace('\\','_',get_class($this->owner)) . '[clearAttachment]' . '[' . $this->name . ']';
    }
}