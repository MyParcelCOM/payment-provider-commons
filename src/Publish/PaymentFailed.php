<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use Override;

class PaymentFailed extends Message
{
    public function __construct(
        private readonly string $myparcelcomPaymentId,
        private readonly FailureCode $failureCode,
        private readonly ?string $failureMessage = null,
        ?string $topicArn = null,
    ) {
        parent::__construct($topicArn);
    }

    #[Override]
    public function payload(): array
    {
        return [
            'myparcelcom_payment_id' => $this->myparcelcomPaymentId,
            'failure_code'           => $this->failureCode->value,
            'failure_message'        => $this->failureMessage,
        ];
    }
}
