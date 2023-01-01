<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParsingLink extends Model
{
    protected $fillable = ['link', 'parsed', 'store_id', 'category_id'];
}
