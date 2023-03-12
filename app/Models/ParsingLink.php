<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParsingLink extends Model
{
    protected $table = 'parsing_links';
    protected $fillable = ['link', 'parsed', 'store_id', 'category_id'];
}
