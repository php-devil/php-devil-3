<?php
namespace PhpDevil\framework\base;

use Exception;
use PhpDevil\framework\common\EntityType;

class InvalidPropertyException extends \Exception
{
    private $entityType    = null;
    private $propertyName  = null;
    private $propertyValue = null;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (is_array($message)) {
            $this->entityType    = $message[0] ?: EntityType::UNDEFINED;
            $this->propertyName  = $message[1] ?: 'undefined';
            $this->propertyValue = $message[2] ?: 'undefined';
            $message = 'Invalid Property Exception';
        }
        parent::__construct($message, $code, $previous);
    }
}