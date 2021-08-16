<?php

use Adapters\Repositories\UserRepository;
use Domain\Entities\User;
use Domain\Usecase\UserRegistration;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;

    public function testShouldReturnUserSuccessful()
    {
        $this->json('POST', '/api/user', [
            'name' => 'Paulo',
            'email' => 'paulo1223@gmail.com',
            'password' => '123456',
            'register_number' => '01234567890',
            'type' => User::CUSTOMER,
            'funds' => 1000
        ])->seeJson([
            "message" => "User registration successful!"
        ]);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    public function testShouldCpfAlreadyRegistered()
    {
        $repository = new UserRepository();
        $userRegistration = new UserRegistration($repository);
        $userRegistration->handle('Paulo', 'paulo1223@gmail.com', '123456', '01234567890', User::CUSTOMER, 500);
        $this->json('POST', '/api/user', [
            'name' => 'Paulo',
            'email' => 'paulo@gmail.com',
            'password' => '123456',
            'register_number' => '01234567890',
            'type' => 1,
            'funds' => 500
        ])->seeJson([
            "message" => ["register_number" => ["The register number has already been taken."]]
        ]);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShouldEmailAlreadyRegistered()
    {
        $repository = new UserRepository();
        $userRegistration = new UserRegistration($repository);
        $userRegistration->handle('Paulo', 'paulo1223@gmail.com', '123456', '01234567890', User::CUSTOMER, 500);
        $this->json('POST', '/api/user', [
            'name' => 'Paulo',
            'email' => 'paulo1223@gmail.com',
            'password' => '123456',
            'register_number' => '01234567891',
            'type' => 1,
            'funds' => 500
        ])->seeJson([
            "message" => ["email" => ["The email has already been taken."]]
        ]);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
