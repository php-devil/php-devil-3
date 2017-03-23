<?php
namespace PhpDevil\framework\web\asset\core;
use PhpDevil\framework\web\asset\AssetBundle;

class JqueryBundle extends AssetBundle
{
    public static function name()
    {
        return 'jquery';
    }

    public static function js()
    {
        return [
            '//code.jquery.com/jquery-2.2.4.min.js',
        ];
    }
}