<?php

declare(strict_types=1);

namespace Domain\Usecase;

use Domain\Entities\User;
use Domain\Repositories\UserRegistrationRepository;

class UserRegistration
{
    private UserRegistrationRepository $registrationRepository;

    public function __construct(UserRegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    public function handle(string $name, string $email, string $password, string $cpf, int $type, int $funds): User
    {
        return $this->registrationRepository->save($name, $email, $password, $cpf, $type, $funds);
    }
}