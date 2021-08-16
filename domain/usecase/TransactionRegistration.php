<?php

namespace Domain\Usecase;

use Domain\Contracts\AuthorizationTransactionService;
use Domain\Contracts\NotifyTransactionService;
use Domain\Entities\Transaction;
use Domain\Exceptions\NotAuthorizedTransactionException;
use Domain\Repositories\TransactionRegistrationRepository;
use Domain\ValueObjects\Cpf;

class TransactionRegistration
{
    private TransactionRegistrationRepository $repository;
    private AuthorizationTransactionService $service;
    private NotifyTransactionService $notify;

    public function __construct(
        TransactionRegistrationRepository $repository,
        AuthorizationTransactionService $service,
        NotifyTransactionService $notify
    ) {
        $this->repository = $repository;
        $this->service = $service;
        $this->notify = $notify;
    }

    /**
     * @throws NotAuthorizedTransactionException
     */
    public function handle(Cpf $payer, Cpf $payee, int $value): Transaction
    {
        if (!$this->service->isAuthorized()) {
            throw new NotAuthorizedTransactionException();
        }

        $transaction = $this->repository->save($payer, $payee, $value);

        $this->notify->send($payer, $payee, 'transaction');

        return $transaction;
    }
}
