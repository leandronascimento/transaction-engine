<?php

declare(strict_types=1);

namespace Adapters\Services;

use Domain\Contracts\AuthorizationTransactionService;

class AuthorizationService implements AuthorizationTransactionService
{

    public function isAuthorized(): bool
    {
        
        return true;
    }
}