<?php

namespace Adapters\Repositories;

use Domain\Entities\User;
use Domain\Exceptions\InvalidCpfException;
use Domain\Repositories\UserRegistrationRepository;
use Domain\ValueObjects\Cpf;
use Illuminate\Support\Facades\DB;

final class UserRepository implements UserRegistrationRepository
{
    public function save(string $name, string $email, string $password, string $cpf, int $type, int $funds): User
    {
        try {
            $record = DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'cpf' => $cpf,
                'type' => $type,
                'funds' => $funds
            ]);
            if ($record) {
                return new User(
                    $name,
                    $email,
                    $password,
                    new Cpf($cpf),
                    $type,
                    $funds
                );
            }
        } catch (InvalidCpfException $e) {
        }
    }

    /**
     * @throws InvalidCpfException
     */
    public function get(Cpf|string $cpf): User
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

    public function updateFunds(Cpf $cpf, int $value): bool
    {
        $record = DB::table('users')->where(['cpf' => $cpf])->first();
        $funds = $record->funds + $value;
        return DB::table('users')->where(['cpf' => $cpf])->update(['funds' => $funds]);
    }
}
