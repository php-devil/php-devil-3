<?php
function smarty_function_asset($params)
{
    if (is_string($params['src']) && 0 === strpos($params['src'], '//')) {
        return $params['src'];
    }
    // публикация ресурса по запросу
    
}