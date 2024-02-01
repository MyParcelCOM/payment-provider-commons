<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use Aws\Sns\SnsClient;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Utils;
use Illuminate\Support\Env;

class Publisher
{
    public function __construct(
        private readonly SnsClient $snsClient,
        private readonly LocalClient $localClient,
    ) {
    }

    public function publish(Message $message): PromiseInterface
    {
        $payload = [
            'type' => $message->getType(),
            'data' => $message->payload(),
        ];

        if (Env::get('APP_ENV') === 'local') {
            return $this->localClient->publish($payload);
        }

        return $this->snsClient->publishAsync([
            'Message'  => Utils::jsonEncode($payload),
            'TopicArn' => $message->getTopicArn(),
        ]);
    }
}
