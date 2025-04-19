<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ActiveIngredientsGroupIngredient extends Pivot
{
    public const TABLE = 'active_ingredients_group_ingredient';

    protected $table = self::TABLE;

    protected $fillable = ['ingredient_id', 'active_ingredients_group_id'];
}
