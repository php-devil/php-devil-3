<?php
namespace PhpDevil\framework\components;

use Exception;

class InvalidInterfaceException extends \Exception
{
    private $instanceName = null;

    private $class = null;

    private $interface = null;

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (is_array($message)) {
            $this->instanceName = $message[0];
            $this->class        = $message[1];
            $this->interface    = $message[2];
            $message = "Требуется реализация интерфейса";
        }
        parent::__construct($message, $code, $previous);
    }
}