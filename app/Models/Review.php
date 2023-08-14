<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
    public const TABLE = 'reviews';

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


    protected $table = self::TABLE;

    protected $fillable = [
        'sku_id',
        'user_id',
        'rating',
        'title',
        'plus',
        'minus',
        'body',
        'is_recommend',
        'status',
    ];

    protected $casts = [
        'body' => 'array',
    ];

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * @return HasMany
     */
    public function sku(): HasMany
    {
        return $this->hasMany(Sku::class);
    }
}
