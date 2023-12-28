<?php

namespace MyParcelCom\Payments\Providers\Providers;

use Aws\Sns\SnsClient;
use Illuminate\Support\ServiceProvider;
use MyParcelCom\Payments\Providers\Sns\Publisher;

class SnsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Publisher::class, fn () => new Publisher(new SnsClient(config('sns'))));
    }
}
