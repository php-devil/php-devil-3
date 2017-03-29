<?php
namespace PhpDevil\framework\components\webuser\storage;

class SessionStorage
{
    private $storageKey = 'UserData';

    public function load()
    {
        return \Devil::app()->session->getValue($this->storageKey);
    }

    public function save($id, $key)
    {
        \Devil::app()->session->setValue($this->storageKey, ['id' => $id, 'auth_key' => $key]);
    }

    public function __construct($storageKey = 'UserData')
    {
        $this->storageKey = $storageKey;
    }
}