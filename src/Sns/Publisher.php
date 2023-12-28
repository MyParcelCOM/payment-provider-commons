<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Sns;

use Aws\Sns\SnsClient;
use DateTimeInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Utils;

class Publisher
{
    public function __construct(
        private readonly SnsClient $snsClient,
    ) {
    }

    public function publish(string $topicArn, string $myparcelcomPaymentId, ?DateTimeInterface $paidAt = null): Promise
    {
        $payload = [
            'myparcelcom_payment_id' => $myparcelcomPaymentId,
            'paid_at'                => $paidAt?->format(DateTimeInterface::ATOM),
        ];

        return $this->snsClient->publishAsync([
            'Message'  => Utils::jsonEncode(array_filter($payload, static fn ($value) => $value !== null)),
            'TopicArn' => $topicArn,
        ]);
    }
}
