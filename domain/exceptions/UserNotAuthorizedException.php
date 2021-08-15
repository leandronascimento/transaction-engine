<?php

namespace Domain\Exceptions;

use Exception;
use Throwable;

class UserNotAuthorizedException extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Transaction not authorized for this user type!', $code, $previous);
    }
}