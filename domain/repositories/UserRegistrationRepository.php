<?php

namespace Domain\Repositories;

use Domain\Entities\User;
use Domain\ValueObjects\Cnpj;
use Domain\ValueObjects\Cpf;

interface UserRegistrationRepository
{
    public function save(
        string $name,
        string $email,
        string $password,
        string $registerNumber,
        int $type,
        int $funds
    ): User;
    public function get(Cpf|Cnpj $registerNumber): User;
    public function updateFunds(Cpf|Cnpj $registerNumber, int $value): User;
}
