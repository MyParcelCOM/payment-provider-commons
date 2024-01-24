<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Sns;

use Aws\Sns\SnsClient;
use DateTimeInterface;
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

    public function publish(string $topicArn, string $myparcelcomPaymentId, ?DateTimeInterface $paidAt = null): PromiseInterface
    {
        $payload = [
            'myparcelcom_payment_id' => $myparcelcomPaymentId,
            'paid_at'                => $paidAt?->format(DateTimeInterface::ATOM),
        ];

        if (Env::get('APP_ENV') === 'local') {
            return $this->localClient->publish($payload);
        }

        return $this->snsClient->publishAsync([
            'Message'  => Utils::jsonEncode(array_filter($payload, static fn ($value) => $value !== null)),
            'TopicArn' => $topicArn,
        ]);
    }
}
