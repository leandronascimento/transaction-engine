<?php

namespace Domain\Repositories;

use Domain\Entities\User;

interface UserRegistrationRepository
{
    public function save(string $name, string $email, string $password, string $cpf, int $type, int $funds): User;
    public function get(string $cpf): User;
}