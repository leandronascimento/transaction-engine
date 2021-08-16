<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

class TransactionApiTest extends TestCase
{
    use DatabaseTransactions;

    public function testShouldReturnTransactionSuccessful()
    {
        $this->json('POST', '/api/transaction', [
            'value' => 50,
            'payer' => '91263413013',
            'payee' => '11591323053'
        ])->seeJson([
            "message" => "Transaction successful!"
        ]);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }
}
