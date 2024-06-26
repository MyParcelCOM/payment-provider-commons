<?php

declare(strict_types=1);

namespace Tests\Publish;

use Aws\Sns\SnsClient;
use Faker\Factory;
use GuzzleHttp\Promise\Promise;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MyParcelCom\Payments\Providers\Publish\AuthorizationDisruption;
use MyParcelCom\Payments\Providers\Publish\FailureCode;
use MyParcelCom\Payments\Providers\Publish\LocalClient;
use MyParcelCom\Payments\Providers\Publish\PaymentFailed;
use MyParcelCom\Payments\Providers\Publish\PaymentSuccessful;
use MyParcelCom\Payments\Providers\Publish\Publisher;
use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_publishes_a_success_message_to_sns(): void
    {
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $myparcelcomPaymentId = $faker->uuid();
        $paidAt = $faker->dateTime();

        $snsClient = Mockery::mock(SnsClient::class, function (MockInterface & SnsClient $mock) use (
            $topicArn,
            $paidAt,
            $myparcelcomPaymentId
        ) {
            $mock
                ->expects('publishAsync')
                ->with([
                    'Message'  => json_encode([
                        'type' => 'payment_successful',
                        'data' => [
                            'myparcelcom_payment_id' => $myparcelcomPaymentId,
                            'paid_at'                => $paidAt->format(DATE_ATOM),
                        ],
                    ], JSON_THROW_ON_ERROR),
                    'TopicArn' => $topicArn,
                ])
                ->andReturns(Mockery::mock(Promise::class));
        });

        $message = new PaymentSuccessful($myparcelcomPaymentId, $paidAt, $topicArn);

        $publisher = new Publisher($snsClient, Mockery::mock(LocalClient::class));
        $publisher->publish($message);
    }

    public function test_it_publishes_a_failure_message_to_sns(): void
    {
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $myparcelcomPaymentId = $faker->uuid();
        $failureCode = $faker->randomElement(FailureCode::cases());
        $failureMessage = $faker->sentence();

        $snsClient = Mockery::mock(SnsClient::class, function (MockInterface & SnsClient $mock) use (
            $topicArn,
            $myparcelcomPaymentId,
            $failureCode,
            $failureMessage
        ) {
            $mock
                ->expects('publishAsync')
                ->with([
                    'Message'  => json_encode([
                        'type' => 'payment_failed',
                        'data' => [
                            'myparcelcom_payment_id' => $myparcelcomPaymentId,
                            'failure_code'           => $failureCode,
                            'failure_message'        => $failureMessage,
                        ],
                    ], JSON_THROW_ON_ERROR),
                    'TopicArn' => $topicArn,
                ])
                ->andReturns(Mockery::mock(Promise::class));
        });

        $message = new PaymentFailed($myparcelcomPaymentId, $failureCode, $failureMessage, $topicArn);

        $publisher = new Publisher($snsClient, Mockery::mock(LocalClient::class));
        $publisher->publish($message);
    }

    public function test_it_publishes_authorization_disruption_to_sns(): void
    {
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $shopId = $faker->uuid;

        $snsClient = Mockery::mock(SnsClient::class, function (MockInterface & SnsClient $mock) use (
            $topicArn,
            $shopId
        ) {
            $mock
                ->expects('publishAsync')
                ->with([
                    'Message'  => json_encode([
                        'type' => 'authorization_disruption',
                        'data' => [
                            'shop_id'               => $shopId,
                            'payment_provider_code' => 'mollie',
                        ],
                    ], JSON_THROW_ON_ERROR),
                    'TopicArn' => $topicArn,
                ])
                ->andReturns(Mockery::mock(Promise::class));
        });

        $message = new AuthorizationDisruption($shopId, 'mollie', $topicArn);

        $publisher = new Publisher($snsClient, Mockery::mock(LocalClient::class));
        $publisher->publish($message);
    }

    public function test_it_publishes_success_message_to_local_client(): void
    {
        putenv('APP_ENV=local');
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $myparcelcomPaymentId = $faker->uuid();
        $paidAt = $faker->dateTime();

        $localClient = Mockery::mock(LocalClient::class, function (MockInterface & LocalClient $mock) use (
            $myparcelcomPaymentId,
            $paidAt
        ) {
            $mock
                ->expects('publish')
                ->with([
                    'type' => 'payment_successful',
                    'data' => [
                        'myparcelcom_payment_id' => $myparcelcomPaymentId,
                        'paid_at'                => $paidAt->format(DATE_ATOM),
                    ],
                ]);
        });

        $message = new PaymentSuccessful($myparcelcomPaymentId, $paidAt, $topicArn);

        $publisher = new Publisher(Mockery::mock(SnsClient::class), $localClient);
        $publisher->publish($message);

        putenv('APP_ENV=');
    }

    public function test_it_publishes_fail_message_to_local_client(): void
    {
        putenv('APP_ENV=local');
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $myparcelcomPaymentId = $faker->uuid();
        /** @var FailureCode $failureCode */
        $failureCode = $faker->randomElement(FailureCode::cases());
        $failureMessage = $faker->sentence();

        $localClient = Mockery::mock(LocalClient::class, function (MockInterface & LocalClient $mock) use (
            $myparcelcomPaymentId,
            $failureCode,
            $failureMessage
        ) {
            $mock
                ->expects('publish')
                ->with([
                    'type' => 'payment_failed',
                    'data' => [
                        'myparcelcom_payment_id' => $myparcelcomPaymentId,
                        'failure_code'           => $failureCode->value,
                        'failure_message'        => $failureMessage,
                    ],
                ]);
        });

        $message = new PaymentFailed($myparcelcomPaymentId, $failureCode, $failureMessage, $topicArn);

        $publisher = new Publisher(Mockery::mock(SnsClient::class), $localClient);
        $publisher->publish($message);

        putenv('APP_ENV=');
    }

    public function test_it_publishes_authorization_disruption_to_local_client(): void
    {
        putenv('APP_ENV=local');
        $faker = Factory::create();

        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word()}";
        $shopId = $faker->uuid();

        $localClient = Mockery::mock(LocalClient::class, function (MockInterface & LocalClient $mock) use (
            $shopId
        ) {
            $mock
                ->expects('publish')
                ->with([
                    'type' => 'authorization_disruption',
                    'data' => [
                        'shop_id'               => $shopId,
                        'payment_provider_code' => 'mollie',
                    ],
                ]);
        });

        $message = new AuthorizationDisruption($shopId, 'mollie', $topicArn);

        $publisher = new Publisher(Mockery::mock(SnsClient::class), $localClient);
        $publisher->publish($message);

        putenv('APP_ENV=');
    }
}
