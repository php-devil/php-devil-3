<?php
namespace PhpDevil\framework\helpers;

class NamesHelper
{
    public static function urlToClass($url)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $url)));
    }

    /**
     * Транслитерация строки
     * ($skipSuffix - для имен файлов - оставить без изменения все, что после последней точки)
     * @param $value
     * @param bool $skipSuffix
     * @return mixed|string
     */
    public static function transliterate($value, $skipSuffix = false)
    {
        if ($skipSuffix) {
            if (false !== ($dot = strrpos($value, '.'))) {
                $suffix = substr($value, $dot);
                $value = substr($value, 0, $dot);
            }
        } else {
            $suffix = '';
        }
        $pre = str_replace(array(
            '?','!','.',',',':',';','*','(',')','{','}','%','#','№','@','$','^','-','+','/','\\','=','|','"','\'',
            'а','б','в','г','д','е','ё','з','и','й','к',
            'л','м','н','о','п','р','с','т','у','ф','х',
            'ъ','ы','э',' ','ж','ц','ч','ш','щ','ь','ю','я'
        ), array(
            '','','','','','-','','','','','','','','','','','','-','','','','','','','',
            'a','b','v','g','d','e','e','z','i','y','k',
            'l','m','n','o','p','r','s','t','u','f','h',
            'j','i','e','-','zh','ts','ch','sh','shch',
            '','yu','ya'
        ), mb_strtolower($value)) . $suffix;
        while (false !== strrpos($pre, '--')) $pre = str_replace('--', '-', $pre);
        return $pre;
    }
}