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
        $extension = Devil::app()->page->getSearchExtension();
        if ($extension) {
            $extViewsLocation = $extension->getLocation() . '/views';
            $extension = $extension->getTagName();
        }
        $theme    = Devil::app()->page->getThemeName();
        $applicationViewsPath = Devil::app()->getViewsLocation() . '';
        $moduleViewsLocation = null;
        if ($module = $renderer->getOwner()->getTagName()) {
            $moduleViewsLocation = $renderer->getViewsLocation();
            if ($theme) $sourceSearch[] = $applicationViewsPath . '/theme-' . $theme . '/modules/' . $module . '/' . $name . '.tpl';
            $sourceSearch[] = $applicationViewsPath . '/modules/' . $module . '/' . $name . '.tpl';
            if ($extension) {
                if ($theme) $sourceSearch[] = $applicationViewsPath . '/theme-' . $theme . '/modules/' . $extension . '/' . $name . '.tpl';
                $sourceSearch[] = $applicationViewsPath . '/modules/' . $extension . '/' . $name . '.tpl';
            }

            if ($theme) $sourceSearch[] = $moduleViewsLocation  . '/theme-' . $theme . '/' . $name . '.tpl';
            $sourceSearch[] = $moduleViewsLocation . '/' . $name . '.tpl';
            if ($extension) {
                if ($theme) $sourceSearch[] = $extViewsLocation  . '/theme-' . $theme . '/' . $name . '.tpl';
                $sourceSearch[] = $extViewsLocation . '/' . $name . '.tpl';
            }
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

        if (!$mtime) {
            print_r($sourceSearch);
        }
    }
}