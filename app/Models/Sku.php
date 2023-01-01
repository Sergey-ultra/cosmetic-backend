<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $fillable = ['product_id', 'images', 'volume', 'rating', 'reviews_count'];

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
        return $this->hasMany(PriceHistory::class)->whereNotNull('price');
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
