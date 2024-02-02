<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Message $message)
    {
    }

    public function handle(Publisher $publisher): void
    {
        $publisher
            ->publish($this->message)
            ->wait();
    }
}
