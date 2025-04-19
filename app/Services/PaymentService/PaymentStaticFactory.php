<?php

declare(strict_types = 1);

namespace App\Services\PaymentService;

use InvalidArgumentException;

final class PaymentStaticFactory
{
    public static function factory(string $type): IPayment
    {
        return match ($type) {
            'yoomoney' => app(YooMoneyService::class),
            default => throw new InvalidArgumentException('Unknown format given'),
        };
    }
}

