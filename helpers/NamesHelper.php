<?php
namespace PhpDevil\framework\helpers;

class NamesHelper
{
    public static function urlToClass($url)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $url)));
    }
}