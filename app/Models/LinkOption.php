<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkOption extends Model
{
    protected $fillable = ['store_id', 'category_id', 'options'];
}
