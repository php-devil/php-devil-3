<?php
namespace PhpDevil\framework\components\page;

interface Renderable
{
    /**
     * Определение местонахождения файла класса
     * @return mixed
     */
    public function getViewsLocation();

    /**
     * Тег класса, выводящего шаблон (поддиректория директории представлений)
     * @return mixed
     */
    public function getTagName();
}