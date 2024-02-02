<?php

namespace MyParcelCom\Payments\Providers\Providers;

use Aws\Sns\SnsClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use MyParcelCom\Payments\Providers\Publish\LocalClient;
use MyParcelCom\Payments\Providers\Publish\Publisher;

class SnsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Publisher::class, fn () => new Publisher(
            new SnsClient(config('publish.sns')),
            new LocalClient(new Client(['base_uri' => config('publish.local.base_uri')])),
        ));
    }
}
