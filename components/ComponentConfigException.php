<?php
namespace PhpDevil\framework\components;

use Exception;

class ComponentConfigException extends \Exception
{
    const INVALID_PARAMETER = 1;
    const INVALID_PARAMETER_VALUE = 2;

    private static $messages = [
        self::INVALID_PARAMETER       => 'Недопустимый параметр компонента',
        self::INVALID_PARAMETER_VALUE => 'Недопустимое значение параметра компонента'
    ];

    private $className;

    private $paramName;

    private $paramValue;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (is_array($message)) {
            $code = $message[0];
            $this->className  = $message[1];
            $this->paramName  = $message[2];
            $this->paramValue = $message[3];
            $message = static::$messages[$code];
        }
        parent::__construct($message, $code, $previous);
    }
}