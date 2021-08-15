<?php
declare(strict_types=1);

namespace Domain\Entities;

use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\UserNotAuthorizedException;

class Transaction
{
    private User $payer;
    private User $payee;
    private int $value;

    /**
     * @throws UserNotAuthorizedException
     * @throws InsufficientFundsException
     */
    public function __construct(User $payer, User $payee, int $value)
    {
        if ($payer->getType() !== User::CUSTOMER) {
            throw new UserNotAuthorizedException();
        }

        if ($payer->getFunds() < $value) {
            throw new InsufficientFundsException();
        }

        $this->payer = $payer;
        $this->payee = $payee;
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getPayee(): User
    {
        return $this->payee;
    }

    public function setPayee(User $payee): void
    {
        $this->payee = $payee;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function setPayer(User $payer): void
    {
        $this->payer = $payer;
    }
}