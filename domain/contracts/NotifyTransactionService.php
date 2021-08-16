<?php

declare(strict_types=1);

namespace Domain\Contracts;

interface NotifyTransactionService
{
    public function send(string $from, string $for, string $message): bool;
}
