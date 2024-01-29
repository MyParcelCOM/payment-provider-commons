<?php

declare(strict_types=1);

namespace Sns;

use Faker\Factory;
use GuzzleHttp\Promise\Promise;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MyParcelCom\Payments\Providers\Sns\Publisher;
use MyParcelCom\Payments\Providers\Sns\PublishJob;
use PHPUnit\Framework\TestCase;

class PublishJobTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_handles_publish_job(): void
    {
        $faker = Factory::create();
        $topicArn = "arn:aws:sns:eu-west-1:{$faker->randomNumber()}:{$faker->word}";
        $myparcelcomPaymentId = $faker->uuid;
        $paidAt = $faker->dateTime;

        $snsPromise = Mockery::mock(Promise::class, function (MockInterface & Promise $mock) {
            $mock->expects('wait');
        });

        $publisher = Mockery::mock(Publisher::class, function (MockInterface & Publisher $mock) use (
            $snsPromise,
            $topicArn,
            $myparcelcomPaymentId,
            $paidAt
        ) {
            $mock->expects('publish')
                ->with(
                    $topicArn,
                    $myparcelcomPaymentId,
                    $paidAt,
                    null,
                    null,
                )
                ->andReturns($snsPromise);
        });

        $job = new PublishJob($topicArn, $myparcelcomPaymentId, $paidAt);
        $job->handle($publisher);
    }
}
