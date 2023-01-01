<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    protected $fillable = ['name', 'link', 'image', 'rating', 'status', 'price_parsing_status', 'check_images_count'];

    public function skus():BelongsToMany
    {
        return $this->belongsToMany(Sku::class);
    }
}
