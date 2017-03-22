<?php
namespace PhpDevil\framework\base;

use Exception;

class ApplicationException extends \Exception
{
    const APPLICATION_TWICE_REGISTRATION = 1;

    private static $_messages = [
        self::APPLICATION_TWICE_REGISTRATION => 'Невозможно запустить два приложения одновременно',
    ];

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (isset(self::$_messages[$message])) $message = self::$_messages[$message];
        parent::__construct($message, $code, $previous);
    }
}