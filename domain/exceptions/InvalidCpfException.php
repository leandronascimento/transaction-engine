<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

class InvalidCpfException extends Exception
{

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid CPF', $code, $previous);
    }
}
