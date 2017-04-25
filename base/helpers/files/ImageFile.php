<?php
namespace PhpDevil\framework\base\helpers\files;

class ImageFile extends AbstractFile
{
    public function resizeByWidth($src, $dest, $width)
    {
        $rgb=0xFFFFFF;
        $quality=100;
        if (!file_exists($src)) return false;
        $size = getimagesize($src);
        if ($size === false) return false;
        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
        $icfunc = "imagecreatefrom" . $format;
        if (!function_exists($icfunc)) return false;
        $ratio = $width / $size[0];
        $height  = floor($size[1] * $ratio);
        $new_left    = 0;
        $new_top     = 0;
        $isrc = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);
        imagefill($idest, 0, 0, $rgb);
        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
            $width, $height, $size[0], $size[1]);
        imagejpeg($idest, $dest, $quality);
        imagedestroy($isrc);
        imagedestroy($idest);
        return true;
    }

    public function resize($sourceFile, $originRoot, $options, $destFile)
    {
        if (!isset($options['thumb'])) $options['thumb'] = ['method'=>'width', 'w'=>320];
        foreach ($options as $size=>$param) {
            $dest = $originRoot . '/' . $size;
            if (!is_dir($dest)) mkdir($dest, 0777, true);
            $dest .= '/' . $destFile;
            switch ($param['method']) {
                case 'width':
                    $this->resizeByWidth($sourceFile, $dest, $param['w']);
                    break;
            }
        }
    }

    public function resizeRemove($destRoot, $fileName, $options)
    {

        if (!isset($options['thumb'])) $options['thumb'] = ['method'=>'width', 'w'=>120];
        foreach ($options as $size=>$param) {
            $deleteName = $destRoot . '/' . $size . '/' . $fileName;
            if (file_exists($deleteName)) unlink($deleteName);
        }
    }
}