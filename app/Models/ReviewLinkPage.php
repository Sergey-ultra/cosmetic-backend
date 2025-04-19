<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewLinkPage extends Model
{
    public const TABLE = 'review_link_pages';

    protected $table = self::TABLE;

    protected $fillable = ['review_link_option_id', 'page_number', 'body'];

    protected $casts = [
        'review_link_option_id' => 'integer',
        'page_number' => 'integer',
        'body' => 'array'
    ];
}
