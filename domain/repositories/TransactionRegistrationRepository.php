<?php

namespace Domain\Repositories;

use Domain\Entities\Transaction;
use Domain\ValueObjects\Cpf;

interface TransactionRegistrationRepository
{
    public function save(Cpf $payer, Cpf $payee, int $value): Transaction;
    public function getByPayer(Cpf $payer): Transaction;
}