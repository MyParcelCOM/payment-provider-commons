<?php

declare(strict_types=1);

namespace Tests\Sns;

use Aws\Sns\SnsClient;
use Faker\Factory;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Utils;
use Illuminate\Support\Env;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MyParcelCom\Payments\Providers\Sns\LocalClient;
use MyParcelCom\Payments\Providers\Sns\Publisher;
use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_publishes_a_message_to_sns(): void
    {
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word}";
        $myparcelcomPaymentId = $faker->uuid;
        $paidAt = $faker->dateTime;

        $snsClient = Mockery::mock(SnsClient::class, function (MockInterface & SnsClient $mock) use (
            $topicArn,
            $paidAt,
            $myparcelcomPaymentId
        ) {
            $mock
                ->expects('publishAsync')
                ->with([
                    'Message'  => json_encode([
                        'myparcelcom_payment_id' => $myparcelcomPaymentId,
                        'paid_at'                => $paidAt->format(DATE_ATOM),
                    ], JSON_THROW_ON_ERROR),
                    'TopicArn' => $topicArn,
                ])
                ->andReturns(Mockery::mock(Promise::class));
        });

        $publisher = new Publisher($snsClient, Mockery::mock(LocalClient::class));
        $publisher->publish($topicArn, $myparcelcomPaymentId, $paidAt);
    }

    public function test_it_publishes_message_to_sns_without_paid_at(): void
    {
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word}";
        $myparcelcomPaymentId = $faker->uuid;

        $snsClient = Mockery::mock(SnsClient::class, function (MockInterface & SnsClient $mock) use (
            $topicArn,
            $myparcelcomPaymentId
        ) {
            $mock
                ->expects('publishAsync')
                ->with([
                    'Message'  => json_encode([
                        'myparcelcom_payment_id' => $myparcelcomPaymentId,
                    ], JSON_THROW_ON_ERROR),
                    'TopicArn' => $topicArn,
                ])
                ->andReturns(Mockery::mock(Promise::class));
        });

        $publisher = new Publisher($snsClient, Mockery::mock(LocalClient::class));
        $publisher->publish($topicArn, $myparcelcomPaymentId);
    }

    public function test_it_publishes_message_to_local_client(): void
    {
        putenv('APP_ENV=local');
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word}";
        $myparcelcomPaymentId = $faker->uuid;
        $paidAt = $faker->dateTime;

        $localClient = Mockery::mock(LocalClient::class, function (MockInterface & LocalClient $mock) use (
            $myparcelcomPaymentId,
            $paidAt
        ) {
            $mock
                ->expects('publish')
                ->with([
                    'myparcelcom_payment_id' => $myparcelcomPaymentId,
                    'paid_at'                => $paidAt->format(DATE_ATOM),
                ]);
        });

        $publisher = new Publisher(Mockery::mock(SnsClient::class), $localClient);
        $publisher->publish($topicArn, $myparcelcomPaymentId, $paidAt);

        putenv('APP_ENV=');
    }
}
