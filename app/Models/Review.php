<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['sku_rating_id', 'plus', 'minus', 'comment', 'anonymously', 'images', 'status'];

    protected $casts = [
        'images' => 'array'
    ];

    public function rating()
    {
        return $this->belongsTo(SkuRating::class);
    }
}
