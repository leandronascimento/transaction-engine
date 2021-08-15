<?php

declare(strict_types=1);

use Domain\Entities\User;
use Domain\Exceptions\InvalidCpfException;
use Domain\Usecase\UserRegistration;
use Domain\ValueObjects\Cpf;
use Adapters\Repositories\UserRepository;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testShouldRegisterAnUser(): void
    {
        $repository = new UserRepository();
        $userRegistration = new UserRegistration($repository);
        $register = $userRegistration->handle('Leandro', 'leandro@test.com', '123456', '01234567890', 1, 500);
        $this->assertTrue($register);
    }

    public function testShouldReturnExceptionInvalidCpf(): void
    {
        $this->expectException(InvalidCpfException::class);
        new Cpf('01234567892');
    }

    public function shouldReturnExceptionWhenDuplicatedCpf()
    {
        // não permitir cadastros com o mesmo cpf
    }
}
