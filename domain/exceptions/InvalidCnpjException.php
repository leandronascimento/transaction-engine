<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

class InvalidCnpjException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid CNPJ', $code, $previous);
    }
}