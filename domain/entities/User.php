<?php

declare(strict_types=1);

namespace Domain\Entities;

use Domain\ValueObjects\Cpf;
use Illuminate\Support\Facades\Hash;

final class User
{
    const SHOPKEEPER = 1;
    const CUSTOMER = 2;

    private string $name;
    private string $email;
    private string $password;
    private Cpf $cpf;
    private int $type;
    private int $funds;

    public function __construct(string $name, string $email, string $password, Cpf $cpf, int $type, int $funds)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = Hash::make($password);
        $this->cpf = $cpf;
        $this->type = $type;
        $this->funds = $funds;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getCpf(): Cpf
    {
        return $this->cpf;
    }

    public function setCpf(Cpf $cpf): User
    {
        $this->cpf = $cpf;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getFunds(): int
    {
        return $this->funds;
    }

    public function setFunds(int $funds): void
    {
        $this->funds = $funds;
    }
}
