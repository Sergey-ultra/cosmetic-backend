<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalanceAccrual extends Model
{
    public const TABLE = 'user_balance_accruals';

    public const VIEW_TYPE = 'view';
    public const VIEW_BONUS = 'bonus';
    public const VIEW_REFERRAL = 'referral';

    //85 тысячных рубля
    public const REVIEW_COST = 85;
    public const REFERRAL_COST = 85;

    public const REVIEW_BONUS_COST = 25500;


    protected $table = self::TABLE;

    protected $fillable = ['user_id', 'review_id', 'accrual', 'type', 'date'];
}
