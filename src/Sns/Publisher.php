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

    public function publish(string $topicArn, string $myparcelcomPaymentId, DateTimeInterface $paidAt): Promise
    {
        return $this->snsClient->publishAsync([
            'Message'  => Utils::jsonEncode([
                'myparcelcom_payment_id' => $myparcelcomPaymentId,
                'paid_at'                => $paidAt->format(DateTimeInterface::ATOM),
            ]),
            'TopicArn' => $topicArn,
        ]);
    }
}
