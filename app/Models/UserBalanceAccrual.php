<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalanceAccrual extends Model
{
    public const TABLE = 'user_balance_accruals';

    public const VIEW_TYPE = 'view';

    protected $table = self::TABLE;

    protected $fillable = ['user_id', 'review_id', 'accrual', 'type', 'date'];
}
