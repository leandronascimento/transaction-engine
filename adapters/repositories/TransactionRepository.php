<?php

declare(strict_types=1);

namespace Adapters\Repositories;

use Domain\Entities\Transaction;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\InvalidCpfException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\Repositories\TransactionRegistrationRepository;
use Domain\ValueObjects\Cnpj;
use Domain\ValueObjects\Cpf;
use Exception;
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
     */
    public function save(Cpf $payer, Cpf|Cnpj $payee, int $value): Transaction
    {
        DB::beginTransaction();
        try {
            DB::table('transactions')->insert([
                'payer' => $payer,
                'payee' => $payee,
                'value' => $value,
            ]);

            $transaction = new Transaction(
                $this->userRepository->get($payer),
                $this->userRepository->get($payee),
                $value,
            );

            $this->userRepository->updateFunds($payer, $value * -1);
            $this->userRepository->updateFunds($payee, $value);

            DB::commit();
            return $transaction;
        } catch (InsufficientFundsException | UserNotAuthorizedException | Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
