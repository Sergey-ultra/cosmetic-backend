<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Sku extends Model
{
    protected $fillable = ['product_id', 'images', 'volume', 'rating', 'reviews_count'];

    protected $casts = [
        'images' => 'array'
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => json_decode($attributes['images'], true)[0] ?? null
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class)
            ->withTimestamps()
            ->withPivot(['price', 'link_id', 'created_at'])
            ->using(SkuStore::class);
    }

    public function priceDynamics()
    {
        return $this->hasMany(PriceHistory::class, 'sku_id', 'id')->whereNotNull('price');
    }

    public function prices()
    {
        return $this->hasMany(SkuStore::class)->whereNotNull('price');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


//    public function images()
//    {
//        return $this->morphMany(Image::class, 'imageable');
//    }
}
