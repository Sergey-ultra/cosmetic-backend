<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SkuRating extends Model
{
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
