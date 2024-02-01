<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use DateTimeInterface;
use Override;

class PaymentSuccessful extends Message
{
    public function __construct(
        private readonly string $myparcelcomPaymentId,
        private readonly DateTimeInterface $paidAt,
        ?string $topicArn = null,
    ) {
        parent::__construct($topicArn);
    }

    #[Override]
    public function payload(): array
    {
        return [
            'myparcelcom_payment_id' => $this->myparcelcomPaymentId,
            'paid_at'                => $this->paidAt->format(DateTimeInterface::ATOM),
        ];
    }
}
