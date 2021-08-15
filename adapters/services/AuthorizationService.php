<?php

declare(strict_types=1);

namespace Adapters\Services;

use Domain\Contracts\AuthorizationTransactionService;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationService implements AuthorizationTransactionService
{
    const URL = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

    public function isAuthorized(): bool
    {
        try {
            $client = new Client();
            $response = $client->get(self::URL);
            $body = json_decode($response->getBody()->getContents());

            if ($response->getStatusCode() === Response::HTTP_OK && $body->message === 'Autorizado') {
                return true;
            };

            return false;
        } catch (Exception) {
            throw new Exception('Unavailable service!');
        }
    }
}
