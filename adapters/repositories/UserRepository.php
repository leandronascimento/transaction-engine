<?php

namespace Adapters\Repositories;

use Domain\Entities\User;
use Domain\Exceptions\InvalidCnpjException;
use Domain\Exceptions\InvalidCpfException;
use Domain\Repositories\UserRegistrationRepository;
use Domain\ValueObjects\Cnpj;
use Domain\ValueObjects\Cpf;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserRepository implements UserRegistrationRepository
{
    public function save(
        string $name,
        string $email,
        string $password,
        string $registerNumber,
        int $type,
        int $funds
    ): User {
        DB::beginTransaction();
        try {
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'register_number' => $registerNumber,
                'type' => $type,
                'funds' => $funds
            ]);

            DB::commit();
            return new User(
                $name,
                $email,
                $password,
                $this->getRegisterNumber($type, $registerNumber),
                $type,
                $funds
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function get(Cpf|Cnpj|string $registerNumber): User|null
    {
        $record = DB::table('users')->where(['register_number' => $registerNumber])->first();

        if (is_null($record)) {
            return null;
        }

        return new User(
            $record->name,
            $record->email,
            $record->password,
            $this->getRegisterNumber($record->type, $record->register_number),
            $record->type,
            $record->funds
        );
    }

    public function updateFunds(Cpf|Cnpj $registerNumber, int $value): User
    {
        $record = DB::table('users')->where(['register_number' => $registerNumber])->first();
        $funds = $record->funds + $value;
        DB::table('users')->where(['register_number' => $registerNumber])->update(['funds' => $funds]);

        return new User(
            $record->name,
            $record->email,
            $record->password,
            $this->getRegisterNumber($record->type, $record->register_number),
            $record->type,
            $funds
        );
    }

    public function getRegisterNumber(int $type, string $register): Cpf|Cnpj
    {
        return $type === User::SHOPKEEPER ? new Cnpj($register) : new Cpf($register);
    }
}
