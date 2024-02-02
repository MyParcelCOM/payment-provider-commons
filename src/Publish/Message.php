<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

use Illuminate\Support\Str;

abstract class Message
{
    private readonly string $topicArn;

    public function __construct(?string $topicArn = null)
    {
        $this->topicArn = $topicArn ?? config('publish.sns.topic_arn');
    }

    public function getTopicArn(): string
    {
        return $this->topicArn;
    }

    public function getType(): string
    {
        return strtolower(Str::snake(class_basename($this)));
    }

    abstract public function payload(): array;
}
