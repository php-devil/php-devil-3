<?php
namespace PhpDevil\framework\models\attributes;

class TextAttribute extends StringAttribute
{
    public function getHtmlType()
    {
        return 'textarea';
    }
}