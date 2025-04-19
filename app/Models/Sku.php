<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @params $images картинки
 */
class Sku extends Model
{
    public const TABLE = 'skus';

    protected $table = self::TABLE;

    protected $fillable = ['product_id', 'images', 'volume', 'rating', 'reviews_count', 'status', 'user_id'];

    protected $casts = [
        'images' => 'array'
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => json_decode($attributes['images'], true)[0] ?? null
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class)
            ->withTimestamps()
            ->withPivot(['price', 'link_id', 'created_at'])
            ->using(SkuStore::class);
    }

    public function priceDynamics(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'sku_id', 'id')->whereNotNull('price');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SkuStore::class)->whereNotNull('price');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }


//    public function images()
//    {
//        return $this->morphMany(Image::class, 'imageable');
//    }
}
