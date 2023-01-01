<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'name_rus', 'code', 'description', 'image'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
