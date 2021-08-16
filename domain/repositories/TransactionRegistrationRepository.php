<?php

namespace Domain\Repositories;

use Domain\Entities\Transaction;
use Domain\ValueObjects\Cnpj;
use Domain\ValueObjects\Cpf;

interface TransactionRegistrationRepository
{
    public function save(Cpf $payer, Cpf|Cnpj $payee, int $value): Transaction;
}
