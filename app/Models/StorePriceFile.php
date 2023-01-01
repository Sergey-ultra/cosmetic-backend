<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePriceFile extends Model
{
    protected $fillable = ['name', 'link', 'store_id', 'user_id', 'file_url', 'image', 'status'];
}
