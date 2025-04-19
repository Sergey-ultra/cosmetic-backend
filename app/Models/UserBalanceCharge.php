<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalanceCharge extends Model
{
    public const STATUS_PROCESSED = 'processed';

    public const TABLE = 'user_balance_charge';

    protected $table = self::TABLE;

    protected $fillable = ['uuid', 'user_id', 'wallet_id', 'ordered_amount', 'amount', 'payment_date', 'status'];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class);
    }
}
