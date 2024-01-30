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

    public function publish(
        string $topicArn,
        string $myparcelcomPaymentId,
        ?DateTimeInterface $paidAt = null,
        ?FailureCode $failureCode = null,
        ?string $failureMessage = null,
    ): PromiseInterface {
        if ($paidAt === null && $failureCode === null) {
            throw new EmptyPayloadException();
        }

        $payload = array_filter([
            'myparcelcom_payment_id' => $myparcelcomPaymentId,
            'paid_at'                => $paidAt?->format(DateTimeInterface::ATOM),
            'failure_code'           => $failureCode?->value,
            'failure_message'        => $failureMessage,
        ], static fn ($value) => $value !== null);

        if (Env::get('APP_ENV') === 'local') {
            return $this->localClient->publish($payload);
        }

        return $this->snsClient->publishAsync([
            'Message'  => Utils::jsonEncode($payload),
            'TopicArn' => $topicArn,
        ]);
    }
}
