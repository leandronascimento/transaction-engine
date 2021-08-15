<?php

declare(strict_types=1);

use Adapters\Repositories\UserRepository;
use Adapters\Services\AuthorizationService;
use Adapters\Services\EmailNotifyService;
use Domain\Entities\Transaction;
use Domain\Entities\User;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\NotAuthorizedTransactionException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\Usecase\TransactionRegistration;
use Domain\ValueObjects\Cpf;
use Adapters\Repositories\TransactionRepository;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class TransactionRegistrationTest extends TestCase
{
    use DatabaseTransactions;
    private AuthorizationService|LegacyMockInterface|MockInterface $authorizationService;
    private AuthorizationService|LegacyMockInterface|MockInterface $notifyService;
    private UserRepository $userRepository;
    private User $payer;
    private User $payee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->payer = $this->userRepository->save(
            'Leandro',
            'leandro@test.com',
            '123456',
            '01234567890',
            User::CUSTOMER,
            500
        );
        $this->payee = $this->userRepository->save(
            'Kelly',
            'kelly@test.com',
            '123456',
            '91263413013',
            User::SHOPKEEPER,
            500
        );
        $this->authorizationService = Mockery::mock(AuthorizationService::class);
        $this->authorizationService->shouldReceive('isAuthorized')->andReturn(true);
        $this->notifyService = Mockery::mock(EmailNotifyService::class);
        $this->notifyService->shouldReceive('send')->andReturn(true);
    }

    public function testShouldRegisterATransaction(): void
    {
        $repository = new TransactionRepository($this->userRepository);
        $transactionRegistration = new TransactionRegistration(
            $repository,
            $this->authorizationService,
            $this->notifyService
        );
        $transaction = $transactionRegistration->handle($this->payer->getCpf(), $this->payee->getCpf(), 200);
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
        $payer = $this->userRepository->save('Leandro', 'leandro@test.com', '123456', '30763451096', User::CUSTOMER, 0);
        $repository = new TransactionRepository($this->userRepository);
        $repository->save($payer->getCpf(), $this->payee->getCpf(), 200);
    }

    public function testShouldReturnNotAuthorizedTransactionException(): void
    {
        $this->expectException(NotAuthorizedTransactionException::class);
        $userRepository = new UserRepository();
        $repository = new TransactionRepository($userRepository);
        $service = Mockery::mock(AuthorizationService::class);
        $service->shouldReceive('isAuthorized')->andReturn(false);
        $transactionRegistration = new TransactionRegistration(
            $repository,
            $service,
            $this->notifyService
        );
        $transactionRegistration->handle(new Cpf('01234567890'), new Cpf('91263413013'), 200);
    }

    public function testShouldChangeFundsWhenTransactionSuccess()
    {
        $userRepository = new UserRepository();
        $repository = new TransactionRepository($userRepository);
        $transactionRegistration = new TransactionRegistration(
            $repository,
            $this->authorizationService,
            $this->notifyService
        );
        $transaction = $transactionRegistration->handle($this->payer->getCpf(), $this->payee->getCpf(), 200);
        $payerUpdated = $userRepository->get($this->payer->getCpf());
        $payeeUpdated = $userRepository->get($this->payee->getCpf());
        $this->assertEquals($payerUpdated->getFunds(), $this->payer->getFunds() - $transaction->getValue());
        $this->assertEquals($payeeUpdated->getFunds(), $this->payee->getFunds() + $transaction->getValue());
    }

    public function testShouldNotifyWhenTransactionSuccess()
    {
        $userRepository = new UserRepository();
        $repository = new TransactionRepository($userRepository);
        $transactionRegistration = new TransactionRegistration(
            $repository,
            $this->authorizationService,
            $this->notifyService
        );
        $transaction = $transactionRegistration->handle($this->payer->getCpf(), $this->payee->getCpf(), 200);
        $this->assertTrue($this->notifyService->send($this->payer->getCpf(), $this->payee->getCpf()));
    }
}
