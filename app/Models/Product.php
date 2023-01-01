<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'brand_id', 'name', 'name_en', 'code', 'description',
        'application', 'purpose', 'effect', 'age', 'type_of_skin'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function skus()
    {
        return $this->hasMany(Sku::class)
            ->whereHas('prices')
            ->select(['id','product_id', 'images', 'volume'])
            ->orderBy('volume');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot(['order']);
    }


}
