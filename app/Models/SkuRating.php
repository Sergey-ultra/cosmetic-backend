<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SkuRating extends Model
{
    public const RATING_ONE = 1;
    public const RATING_TWO = 2;
    public const RATING_THREE = 3;
    public const RATING_FOUR = 4;
    public const RATING_FIVE = 5;

    public const RATINGS = [
        self::RATING_ONE,
        self::RATING_TWO,
        self::RATING_THREE,
        self::RATING_FOUR,
        self::RATING_FIVE,
    ];
    public const TABLE = 'sku_ratings';

    protected $table = self::TABLE;

    protected $fillable = ['rating', 'user_id', 'ip_address', 'user_name', 'sku_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function availableReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('status', '<>','deleted');
    }
}
