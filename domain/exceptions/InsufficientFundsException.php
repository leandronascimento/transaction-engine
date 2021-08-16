<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

class InsufficientFundsException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Insufficient funds!', $code, $previous);
    }
}
