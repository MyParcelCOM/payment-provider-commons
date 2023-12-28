<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Http;

use DateTimeInterface;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CreateTransactionResponse implements Responsable
{
    public function __construct(
        private readonly string $transactionId,
        private readonly string $checkoutUrl,
        private readonly DateTimeInterface $expiresAt,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toResponse($request): SymfonyResponse
    {
        return new JsonResponse([
            'data' => [
                'transaction_id' => $this->transactionId,
                'checkout_url'   => $this->checkoutUrl,
                'expires_at'     => $this->expiresAt->format(DateTimeInterface::ATOM),
            ],
        ]);
    }
}
