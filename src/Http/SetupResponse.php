<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SetupResponse implements Responsable
{
    public function __construct(
        private readonly ?string $authorizationUrl = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toResponse($request): SymfonyResponse
    {
        if (!$this->authorizationUrl) {
            return new Response('', SymfonyResponse::HTTP_NO_CONTENT);
        }

        return new JsonResponse([
            'data' => [
                'authorization_url' => $this->authorizationUrl,
            ]
        ]);
    }
}
