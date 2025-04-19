<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    public const TABLE = 'brands';

    protected $table = self::TABLE;
    protected $fillable = [ 'name', 'code', 'description', 'image', 'country_id', 'user_id'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

//    public function images()
//    {
//        return $this->morphMany(Image::class, 'imageable');
//    }
}
