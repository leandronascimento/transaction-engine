<?php

declare(strict_types=1);

use Adapters\Repositories\UserRepository;
use Adapters\Services\AuthorizationService;
use Domain\Entities\Transaction;
use Domain\Entities\User;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\NotAuthorizedTransactionException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\Usecase\TransactionRegistration;
use Domain\ValueObjects\Cpf;
use Adapters\Repositories\TransactionRepository;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TransactionRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testShouldRegisterATransaction(): void
    {
        $userRepository = new UserRepository();
        $payer = $userRepository->save('Leandro', 'leandro@test.com', '123456', '01234567890', User::CUSTOMER, 500);
        $payee = $userRepository->save('Kelly', 'kelly@test.com', '123456', '91263413013', User::SHOPKEEPER, 500);
        $repository = new TransactionRepository($userRepository);
        $service = Mockery::mock(AuthorizationService::class);
        $service->shouldReceive('isAuthorized')->andReturn(true);
        $transactionRegistration = new TransactionRegistration($repository, $service);
        $transaction = $transactionRegistration->handle($payer->getCpf(), $payee->getCpf(), 200);
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testShouldReturnUserNotAuthorizedException(): void
    {
        $this->expectException(UserNotAuthorizedException::class);
        $payer = new User('Leandro', 'leandro@test.com', '123456', new Cpf('01234567890'), User::SHOPKEEPER, 500);
        $payee = new User('Kelly', 'kelly@test.com', '123456', new Cpf('91263413013'), User::SHOPKEEPER, 500);
        new Transaction($payer, $payee, 100);
    }

    public function testShouldReturnInsufficientFundsException(): void
    {
        $this->expectException(InsufficientFundsException::class);
        $userRepository = new UserRepository();
        $payer = $userRepository->save('Leandro', 'leandro@test.com', '123456', '01234567890', User::CUSTOMER, 100);
        $payee = $userRepository->save('Kelly', 'kelly@test.com', '123456', '91263413013', User::SHOPKEEPER, 500);
        $repository = new TransactionRepository($userRepository);
        $repository->save($payer->getCpf(), $payee->getCpf(), 200);
    }

    public function testShouldReturnNotAuthorizedTransactionException(): void
    {
        $this->expectException(NotAuthorizedTransactionException::class);
        $userRepository = new UserRepository();
        $repository = new TransactionRepository($userRepository);
        $service = Mockery::mock(AuthorizationService::class);
        $service->shouldReceive('isAuthorized')->andReturn(false);
        $transactionRegistration = new TransactionRegistration($repository, $service);
        $transactionRegistration->handle(new Cpf('01234567890'), new Cpf('91263413013'), 200);
    }

    public function testShouldChangeFundsWhenTransactionSuccess()
    {
        $userRepository = new UserRepository();
        $payer = $userRepository->save('Leandro', 'leandro@test.com', '123456', '01234567890', User::CUSTOMER, 500);
        $payee = $userRepository->save('Kelly', 'kelly@test.com', '123456', '91263413013', User::SHOPKEEPER, 500);
        $repository = new TransactionRepository($userRepository);
        $service = Mockery::mock(AuthorizationService::class);
        $service->shouldReceive('isAuthorized')->andReturn(true);
        $transactionRegistration = new TransactionRegistration($repository, $service);
        $transaction = $transactionRegistration->handle($payer->getCpf(), $payee->getCpf(), 200);
        $payerUpdated = $userRepository->get($payer->getCpf());
        $payeeUpdated = $userRepository->get($payee->getCpf());
        $this->assertEquals($payerUpdated->getFunds(), $payer->getFunds() - $transaction->getValue());
        $this->assertEquals($payeeUpdated->getFunds(), $payee->getFunds() + $transaction->getValue());
    }
}
