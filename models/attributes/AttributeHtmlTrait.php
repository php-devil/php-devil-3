<?php
namespace PhpDevil\framework\models\attributes;

trait AttributeHtmlTrait
{
    public $htmlType = 'input';

    public function getID()
    {
        return get_class($this->getOwner())::getID() . '_' . $this->name;
    }

    public function getName()
    {
        return get_class($this->getOwner())::getID() . '[' . $this->name . ']';
    }

    public function getCaption()
    {
        return get_class($this->getOwner())::labelOf($this->name);
    }

    public function getHtmlType()
    {
        return $this->htmlType;
    }
}