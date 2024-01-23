<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Sns;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;

class LocalClient
{
    public function __construct(private readonly Client $client)
    {
    }

    public function publish(array $payload): PromiseInterface
    {
        return $this->client->postAsync('/internal/payment-provider-messages', [
            RequestOptions::JSON => [
                'Message' => Utils::jsonEncode($payload),
            ],
        ]);
    }
}
