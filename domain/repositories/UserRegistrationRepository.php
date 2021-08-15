<?php

namespace Domain\Repositories;

interface UserRegistrationRepository
{
    public function save(string $name, string $email, string $password, string $cpf, int $type, int $funds): bool;
}