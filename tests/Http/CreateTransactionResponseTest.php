<?php

declare(strict_types=1);

namespace Tests\Http;

use DateTimeInterface;
use Faker\Factory;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\Payments\Providers\Http\CreateTransactionResponse;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;

class CreateTransactionResponseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_responds_with_transaction_details_json(): void
    {
        $faker = Factory::create();

        $transactionId = $faker->uuid();
        $checkoutUrl = $faker->url();
        $expiresAt = $faker->dateTime();

        $createTransactionResponse = new CreateTransactionResponse(
            transactionId: $transactionId,
            checkoutUrl: $checkoutUrl,
            expiresAt: $expiresAt,
        );

        $response = $createTransactionResponse->toResponse(Mockery::mock(Request::class));

        assertSame(
            expected: [
                'data' => [
                    'transaction_id' => $transactionId,
                    'checkout_url'   => $checkoutUrl,
                    'expires_at'     => $expiresAt->format(DateTimeInterface::ATOM),
                ],
            ],
            actual: $response->getData(true),
        );
    }
}
