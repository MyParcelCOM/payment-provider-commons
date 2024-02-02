<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use Override;

class AuthorizationDisruption extends Message
{
    private readonly string $paymentProviderCode;

    public function __construct(
        private readonly string $shopId,
        ?string $paymentProviderCode = null,
        ?string $topicArn = null,
    ) {
        $this->paymentProviderCode = $paymentProviderCode ?? env('PAYMENT_PROVIDER_CODE');

        parent::__construct($topicArn);
    }

    #[Override]
    public function payload(): array
    {
        return [
            'shop_id'               => $this->shopId,
            'payment_provider_code' => $this->paymentProviderCode,
        ];
    }
}
