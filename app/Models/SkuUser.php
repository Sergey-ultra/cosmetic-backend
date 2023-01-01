<?php
declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

class SkuUser extends Pivot
{
    protected $fillable = ['id', 'sku_id', 'user_id'];
}
