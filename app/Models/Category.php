<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public const TABLE = 'categories';

    protected $table = self::TABLE;

    protected $fillable = ['name', 'code', 'image', 'description', 'parent_id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
