<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Review extends Model
{
    public const TABLE = 'reviews';

    protected $table = self::TABLE;

    protected $fillable = ['sku_rating_id', 'title', 'plus', 'minus', 'body', 'anonymously', 'images', 'status'];

    protected $casts = [
        'images' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function rating(): BelongsTo
    {
        return $this->belongsTo(SkuRating::class);
    }

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
}
