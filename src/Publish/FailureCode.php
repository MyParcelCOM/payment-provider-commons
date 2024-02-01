<?php

declare(strict_types=1);

namespace MyParcelCom\Payments\Providers\Publish;

enum FailureCode: string
{
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
}
