<?php
namespace PhpDevil\framework\models\attributes;

class EditorAttribute extends TextAttribute
{
    public function getHtmlType()
    {
        return 'editor';
    }
}