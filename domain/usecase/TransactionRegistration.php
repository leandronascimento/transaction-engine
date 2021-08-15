<?php

namespace Domain\Usecase;

use Domain\Contracts\AuthorizationTransactionService;
use Domain\Entities\Transaction;
use Domain\Exceptions\NotAuthorizedTransactionException;
use Domain\Repositories\TransactionRegistrationRepository;
use Domain\ValueObjects\Cpf;

class TransactionRegistration
{
    private TransactionRegistrationRepository $transactionRepository;
    private AuthorizationTransactionService $service;

    public function __construct(
        TransactionRegistrationRepository $transactionRepository,
        AuthorizationTransactionService $service
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->service = $service;
    }

    /**
     * @throws NotAuthorizedTransactionException
     */
    public function handle(Cpf $payer, Cpf $payee, int $value): Transaction
    {
        if (!$this->service->isAuthorized()) {
            throw new NotAuthorizedTransactionException();
        }

        return $this->transactionRepository->save($payer, $payee, $value);
    }
}
