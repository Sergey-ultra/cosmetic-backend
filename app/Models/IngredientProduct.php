<?php
declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientProduct extends Pivot
{
    protected $fillable = ['product_id', 'order', 'ingredient_id'];
}
