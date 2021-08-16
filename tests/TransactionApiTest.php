<?php

use Adapters\Repositories\UserRepository;
use Domain\Entities\User;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

class TransactionApiTest extends TestCase
{
    use DatabaseTransactions;

    public function testShouldReturnTransactionSuccessful()
    {
        $userRepository = new UserRepository();
        $payer = $userRepository->save(
            'Leandro',
            'leandro@test.com',
            '123456',
            '53360945018',
            User::CUSTOMER,
            500
        );
        $payee = $userRepository->save(
            'Kelly',
            'kelly@test.com',
            '123456',
            '11444777000161',
            User::SHOPKEEPER,
            500
        );
        $this->json('POST', '/api/transaction', [
            'value' => 50,
            'payer' => '53360945018',
            'payee' => '11444777000161'
        ])->seeJson([
            "message" => "Transaction successful!"
        ]);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }
}
