<?php

namespace Adapters\Repositories;

use Domain\Entities\User;
use Domain\Exceptions\InvalidCpfException;
use Domain\Repositories\UserRegistrationRepository;
use Domain\ValueObjects\Cpf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserRepository implements UserRegistrationRepository
{
    /**
     * @throws InvalidCpfException
     */
    public function save(string $name, string $email, string $password, string $cpf, int $type, int $funds): User
    {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'cpf' => $cpf,
                'type' => $type,
                'funds' => $funds
            ]);

            return new User(
                $name,
                $email,
                $password,
                new Cpf($cpf),
                $type,
                $funds
            );
    }

    /**
     * @throws InvalidCpfException
     */
    public function get(Cpf $cpf): User
    {
        $record = DB::table('users')->where(['cpf' => $cpf])->first();

        return new User(
            $record->name,
            $record->email,
            $record->password,
            new Cpf($record->cpf),
            $record->type,
            $record->funds
        );
    }

    public function updateFunds(Cpf $cpf, int $value): User
    {
        $record = DB::table('users')->where(['cpf' => $cpf])->first();
        $funds = $record->funds + $value;
        DB::table('users')->where(['cpf' => $cpf])->update(['funds' => $funds]);

        return new User(
            $record->name,
            $record->email,
            $record->password,
            new Cpf($record->cpf),
            $record->type,
            $funds
        );
    }
}
