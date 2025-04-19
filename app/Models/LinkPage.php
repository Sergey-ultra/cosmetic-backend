<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkPage extends Model
{
    public const TABLE = 'link_pages';
    protected $table = self::TABLE;
    protected $fillable = ['link_option_id', 'page_number', 'body'];

    protected $casts = [
        'link_option_id' => 'integer',
        'page_number' => 'integer',
        'body' => 'array'
    ];
}
