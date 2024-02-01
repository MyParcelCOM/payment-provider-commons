<?php

declare(strict_types=1);

namespace Sns;

use Faker\Factory;
use GuzzleHttp\Promise\Promise;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MyParcelCom\Payments\Providers\Publish\Message;
use MyParcelCom\Payments\Providers\Publish\Publisher;
use MyParcelCom\Payments\Providers\Publish\PublishJob;
use PHPUnit\Framework\TestCase;

class PublishJobTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_it_handles_publish_job(): void
    {
        $faker = Factory::create();

        $snsPromise = Mockery::mock(Promise::class, function (MockInterface & Promise $mock) {
            $mock->expects('wait');
        });

        $message = Mockery::mock(Message::class);

        $publisher = Mockery::mock(Publisher::class, function (MockInterface & Publisher $mock) use (
            $snsPromise,
            $message) {
            $mock->expects('publish')
                ->with($message)
                ->andReturns($snsPromise);
        });


        $job = new PublishJob($message);
        $job->handle($publisher);
    }
}
