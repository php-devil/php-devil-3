<?php
namespace PhpDevil\framework\models\relations;

use PhpDevil\ORM\models\ActiveRecordInterface;

class BelongsTo extends \PhpDevil\ORM\relations\BelongsTo
{
    public function getVariantsFor(ActiveRecordInterface $row, $template = null)
    {

        $variants = ($this->rightClassName)::findAll()->all()->rows();

        $result = [];
        $rf = $this->rightField;
        $result[] = [
            'key' => 0,
            'value' => '/',
            'level' => 0
        ];
        foreach ($variants as $row) {
            $result[] = [
                'key' => $row->$rf->getValue(),
                'value' => $row->fromTemplate($template),
                'level' => $row->getLevel()
            ];
        }
        return $result;
    }

    public function getHtmlType()
    {
        return 'one_from_list';
    }
}