<?php
/**
 * Ресурс представления
 * с динамическим поиском пути
 */

class Smarty_Resource_View extends Smarty_Resource_Custom
{
    public function fetch($name, &$source, &$mtime)
    {
        $sourceSearch = [];
        $renderer = Devil::app()->page->getRenderer();
        $theme    = Devil::app()->page->getThemeName();
        $applicationViewsPath = Devil::app()->getViewsLocation() . '';
        $moduleViewsLocation = null;
        if ($module = $renderer->getOwner()->getTagName()) {
            $moduleViewsLocation = $renderer->getViewsLocation();
            if ($theme) $sourceSearch[] = $applicationViewsPath . '/theme-' . $theme . '/modules/' . $module . '/' . $name . '.tpl';
            $sourceSearch[] = $applicationViewsPath . '/modules/' . $module . '/' . $name . '.tpl';
            if ($theme) $sourceSearch[] = $moduleViewsLocation  . '/theme-' . $theme . '/' . $name . '.tpl';
            $sourceSearch[] = $moduleViewsLocation . '/' . $name . '.tpl';
        } else {
            if ($theme) $sourceSearch[] = $applicationViewsPath . '/theme-' . $theme . '/' . $name . '.tpl';
            $sourceSearch[] = $applicationViewsPath . '/' . $name . '.tpl';
        } if (0 === strrpos($name, 'layouts/')) {
            $sourceSearch[] = $applicationViewsPath . '/' . $name . '.tpl';
        }
        if (!empty($sourceSearch)) foreach ($sourceSearch as $fileName) if (file_exists($fileName)) {
            $source = file_get_contents($fileName);
            $mtime = filemtime($fileName);
            break;
        }
    }
}