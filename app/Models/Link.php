<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['id',  'link', 'clicks', 'code', 'category_id', 'store_id'];
}
