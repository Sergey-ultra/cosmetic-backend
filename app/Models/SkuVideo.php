<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuVideo extends Model
{
   protected $fillable = ['sku_id', 'user_id', 'video', 'description', 'status'];
}
