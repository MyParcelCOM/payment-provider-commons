<?php

declare(strict_types=1);

namespace Tests\Sns;

use Aws\Sns\SnsClient;
use Faker\Factory;
use GuzzleHttp\Promise\Promise;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
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

        $publisher = new Publisher($snsClient);
        $publisher->publish($topicArn, $myparcelcomPaymentId, $paidAt);
    }

    public function test_it_publishes_without_paid_at(): void
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

        $publisher = new Publisher($snsClient);
        $publisher->publish($topicArn, $myparcelcomPaymentId);
    }
}
