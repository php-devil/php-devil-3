<?php
namespace PhpDevil\framework\web\http;

use Exception;

class HttpException extends \Exception
{
    const NOT_FOUND = 404;
    const DENIED = 403;

    private static $_messages = [
        self::NOT_FOUND => 'Страница не найдена',
        self::DENIED    => 'Доступ запрещен',
    ];

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (isset(self::$_messages[$message])) {
            $message = self::$_messages[$message];
        }
        parent::__construct($message, $code, $previous);
    }
}