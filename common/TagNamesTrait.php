<?php
namespace PhpDevil\framework\common;

/**
 * Class TagNamesTrait
 * Трейт позволяет вести именование объектов по коротким именам - тегам
 * @package PhpDevil\framework\common
 */
trait TagNamesTrait
{
    protected $tagName = null;

    /**
     * Установка тега (ключ вышестоящей конфигурации)
     * @param null $tagName
     */
    public function setTagName($tagName = null)
    {
        $this->tagName = strtolower($tagName);
    }

    /**
     * Получение тега
     * @return null
     */
    public function getTagName()
    {
        return $this->tagName;
    }
}