<?php

declare(strict_types=1);

namespace Domain\Usecase;

use Domain\Entities\User;
use Domain\Repositories\UserRegistrationRepository;

class UserRegistration
{
    private UserRegistrationRepository $repository;

    public function __construct(UserRegistrationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(
        string $name,
        string $email,
        string $password,
        string $registerNumber,
        int $type,
        int $funds
    ): User {
        return $this->repository->save($name, $email, $password, $registerNumber, $type, $funds);
    }
}
