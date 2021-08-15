<?php

namespace Infra\Repositories;

use Domain\Repositories\UserRegistrationRepository;
use Illuminate\Support\Facades\DB;

final class UserRepository implements UserRegistrationRepository
{

    public function save(string $name, string $email, string $password, string $cpf, int $type, int $funds): bool
    {
        return DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'cpf' => $cpf,
            'type' => $type,
            'funds' => $funds
        ]);
    }
}
