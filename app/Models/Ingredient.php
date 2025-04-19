<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['name', 'name_rus', 'code', 'description', 'image'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function activeIds(): HasMany
    {
        return $this->hasMany(ActiveIngredientsGroupIngredient::class);
    }

    public function isActive()
    {
        $this->newQuery()->has('activeIds');
    }
}
