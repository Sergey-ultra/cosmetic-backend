<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkuRating extends Model
{
    protected $fillable = ['rating', 'user_id', 'ip_address', 'user_name', 'sku_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function availableReview()
    {
        return $this->hasOne(Review::class)->where('status', '<>','deleted');
    }
}
