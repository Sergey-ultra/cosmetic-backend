<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActualPriceParsing extends Model
{
    protected $fillable = ['links_by_time'];

    protected $casts = [
        'links_by_time' => 'array'
    ];
}
