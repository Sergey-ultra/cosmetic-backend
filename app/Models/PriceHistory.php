<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $fillable = [
        'id',
        'sku_id',
        'store_id',
        'link_id',
        'price',
        'created_at',
        'updated_at'
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id', 'id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
