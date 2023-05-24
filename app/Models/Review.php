<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = ['sku_rating_id', 'title', 'plus', 'minus', 'comment', 'anonymously', 'images', 'status'];

    protected $casts = [
        'images' => 'array'
    ];

    public function rating(): BelongsTo
    {
        return $this->belongsTo(SkuRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
