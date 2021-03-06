<?php

declare(strict_types=1);

namespace Adapters\Services;

use Domain\Contracts\NotifyTransactionService;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class EmailNotifyService implements NotifyTransactionService
{
    const URL = 'http://o4d9z.mocklab.io/notify';

    public function send(string $from, string $for, string $message): bool
    {
        try {
            $client = new Client();
            $response = $client->get(self::URL);
            $body = json_decode($response->getBody()->getContents());

            if ($response->getStatusCode() === Response::HTTP_OK && $body->message === 'Success') {
                return true;
            };

            return false;
        } catch (Exception) {
            throw new Exception('Unavailable service!');
        }
    }
}
