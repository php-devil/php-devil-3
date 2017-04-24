<?php
namespace PhpDevil\framework\models\attributes;

class FileAttribute extends StringAttribute
{
    public function getUrl($subFolder = 'origin')
    {
        $config = $this->owner->getConfig();
        if (isset($config['media'][$this->name])) {
            $dest = str_replace('@media/', '@mediaURL/', $config['media'][$this->name]['dest']);
            return \Devil::getPathOf($dest) . '/' . $subFolder . '/' . $this->owner->getRoleValue('id') . '_' . $this->getValue();
        }
    }
}