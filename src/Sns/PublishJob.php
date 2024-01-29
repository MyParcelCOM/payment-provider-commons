<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Sns;

use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $topicArn,
        private readonly string $myparcelcomPaymentId,
        private readonly ?DateTimeInterface $paidAt = null,
        private readonly ?FailureCode $failureCode = null,
        private readonly ?string $failureMessage = null,
    ) {
    }

    public function handle(Publisher $publisher): void
    {
        $publisher
            ->publish(
                $this->topicArn,
                $this->myparcelcomPaymentId,
                $this->paidAt,
                $this->failureCode,
                $this->failureMessage,
            )
            ->wait();
    }
}
