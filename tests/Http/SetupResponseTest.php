<?php

declare(strict_types=1);

namespace Tests\Http;

use Faker\Factory;
use Illuminate\Http\Request;
use JsonException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\Payments\Providers\Http\SetupResponse;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class SetupResponseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_returns_no_content_response_when_no_authorization_url_is_set(): void
    {
        $setupResponse = new SetupResponse();

        $response = $setupResponse->toResponse(Mockery::mock(Request::class));

        assertEquals(204, $response->getStatusCode());
        assertEmpty($response->getContent());
    }

    /**
     * @throws JsonException
     */
    public function test_it_returns_json_response_when_authorization_url_is_set(): void
    {
        $faker = Factory::create();

        $authorizationUrl = $faker->url();

        $setupResponse = new SetupResponse($authorizationUrl);

        $response = $setupResponse->toResponse(Mockery::mock(Request::class));

        assertEquals(200, $response->getStatusCode());
        assertEquals(json_encode([
            'data' => [
                'authorization_url' => $authorizationUrl,
            ]
        ], JSON_THROW_ON_ERROR), $response->getContent());
    }
}
