<?php
namespace PhpDevil\framework\models\attributes;

trait AttributeHtmlTrait
{
    protected $_relation = null;

    public function getID()
    {
        return str_replace('\\','_',get_class($this->owner)) . '__' . $this->name;
    }

    public function getCaption()
    {
        return $this->owner->labelOf($this->name);
    }

    public function getName()
    {
        return str_replace('\\','_',get_class($this->owner)) . '[' . $this->name . ']';
    }

    public function getVariants()
    {
        if ($this->isRelated()) {
            return $this->_relation->getVariantsFor($this->owner, $this->config['template']);
        }
    }

    public function getHtmlType()
    {
        if ($this->isRelated()) {
            return $this->_relation->getHtmlType();
        } else {
            return 'input';
        }
    }

    public function isRelated()
    {
        if (null === $this->_relation) {
            if (isset($this->config['relation'])) $this->_relation = $this->owner->getRelation($this->config['relation']);
        }
        return (bool) $this->_relation;
    }
}