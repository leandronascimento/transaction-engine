<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

class NotAuthorizedTransactionException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Transaction not authorized!', $code, $previous);
    }
}
