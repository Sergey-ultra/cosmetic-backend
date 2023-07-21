<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const TABLE = 'products';

    protected $table = self::TABLE;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'name_en',
        'code',
        'user_id',
        'description',
        'application',
        'purpose',
        'effect',
        'age',
        'type_of_skin'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class)
            ->whereHas('prices')
            ->select(['id','product_id', 'images', 'volume'])
            ->orderBy('volume');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)->withPivot(['order']);
    }

    public function ingredientIds(): HasMany
    {
        return $this->hasMany(IngredientProduct::class);
    }
}
