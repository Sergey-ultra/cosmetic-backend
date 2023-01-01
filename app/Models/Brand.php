<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [ 'name', 'code', 'description', 'image', 'country_id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

//    public function images()
//    {
//        return $this->morphMany(Image::class, 'imageable');
//    }
}
