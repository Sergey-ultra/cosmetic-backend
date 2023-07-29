<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserBalanceAccrual;
use App\Models\UserCharge;
use App\Models\UserWallet;
use App\Services\PaymentService\PaymentStaticFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class UserChargeMoneyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(
        protected int $amount,
        protected string $to,
        protected string $type,
        protected string $label
    ){}

    public function handle(PaymentStaticFactory $paymentFactory): void
    {

        $paymentService = $paymentFactory::factory($this->type);
        $paymentService->sendMoney($this->to, $this->amount, $this->label);
    }
}
