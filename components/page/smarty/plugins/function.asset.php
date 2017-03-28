<?php
function smarty_function_asset($params)
{
    if (is_string($params['src']) && 0 === strpos($params['src'], '//')) {
        return $params['src'];
    } else {
        if ($bundle = Devil::app()->page->getAssetBundle($params['src'][0])) {
            return Devil::getPathOf('@assetsUrl') . '/' . $bundle::publishFile($params['src'][1]);
        }
    }
}