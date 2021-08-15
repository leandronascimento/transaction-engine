<?php

declare(strict_types=1);

namespace Adapters\Repositories;

use Domain\Entities\Transaction;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\InvalidCpfException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\Repositories\TransactionRegistrationRepository;
use Domain\ValueObjects\Cpf;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRegistrationRepository
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws InsufficientFundsException
     * @throws InvalidCpfException
     */
    public function save(Cpf $payer, Cpf $payee, int $value): Transaction
    {
        DB::table('transactions')->insert([
            'payer' => $payer,
            'payee' => $payee,
            'value' => $value,
        ]);

        return new Transaction(
            $this->userRepository->get($payer),
            $this->userRepository->get($payee),
            $value,
        );
    }

    /**
     * @throws UserNotAuthorizedException
     * @throws InsufficientFundsException
     * @throws InvalidCpfException
     */
    public function getByPayer(Cpf $payer): Transaction
    {
        $record = DB::table('transactions')->where(['payer' => $payer])->first();

        return new Transaction(
            $this->userRepository->get($record->payer),
            $this->userRepository->get($record->payee),
            $record->value,
        );
    }
}
