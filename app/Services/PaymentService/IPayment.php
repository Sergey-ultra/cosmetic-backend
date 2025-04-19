<?php

namespace App\Services\PaymentService;

interface IPayment
{
    public function sendMoney(int $to, int $amount, string $label): array;
}
