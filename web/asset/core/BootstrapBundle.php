<?php
namespace PhpDevil\framework\web\asset\core;
use PhpDevil\framework\web\asset\AssetBundle;

class BootstrapBundle extends AssetBundle
{
    public static function name()
    {
        return 'bootstrap';
    }

    public static function requirements()
    {
        return [
            JqueryBundle::class,
        ];
    }

    public static function css()
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css',
            '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
        ];
    }

    public static function js()
    {
        return [
            '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js',
        ];
    }
}