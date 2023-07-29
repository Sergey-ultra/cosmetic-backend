<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCharge extends Model
{
    public const TABLE = 'user_charge';

    protected $table = self::TABLE;

    protected $fillable = ['uuid', 'user_id', 'wallet_id', 'ordered_amount', 'amount', 'payment_date', 'status'];
}
