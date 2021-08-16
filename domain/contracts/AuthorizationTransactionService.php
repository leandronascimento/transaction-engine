<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface AuthorizationTransactionService
{
    public function isAuthorized(): bool;
}
