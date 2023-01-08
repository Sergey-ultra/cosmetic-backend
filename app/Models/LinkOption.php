<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkOption extends Model
{
    protected $table = 'link_options';

    protected $fillable = ['store_id', 'category_id', 'options', 'body'];

    protected $casts = [
        'options' => 'array',
        'body' => 'array',
    ];
}
