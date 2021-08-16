<?php

declare(strict_types=1);

namespace Domain\Entities;

use Domain\ValueObjects\Cnpj;
use Domain\ValueObjects\Cpf;

final class User
{
    const SHOPKEEPER = 1;
    const CUSTOMER = 2;

    private string $name;
    private string $email;
    private string $password;
    private int $type;
    private int $funds;
    private Cnpj|Cpf $registerNumber;

    public function __construct(
        string $name,
        string $email,
        string $password,
        Cpf|Cnpj $registerNumber,
        int $type,
        int $funds
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
        $this->funds = $funds;
        $this->registerNumber = $registerNumber;
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

    public function getRegisterNumber(): Cpf|Cnpj
    {
        return $this->registerNumber;
    }

    public function setRegisterNumber(Cpf|Cnpj $registerNumber): void
    {
        $this->registerNumber = $registerNumber;
    }
}
