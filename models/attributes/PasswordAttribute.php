<?php
namespace PhpDevil\framework\models\attributes;

class PasswordAttribute extends StringAttribute
{
    public function getHtmlType()
    {
        return 'password';
    }
}