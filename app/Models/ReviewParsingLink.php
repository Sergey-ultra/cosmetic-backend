<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewParsingLink extends Model
{
    public const UNPARSED = 0;
    public const PARSED = 1;
    public const PUBLISHED = 2;

    public const STATUS_MAP = [
        self::UNPARSED => 'Не обработанная',
        self::PARSED => 'Обработанная',
        self::PUBLISHED => 'Опубликовано',
    ];

    protected $table = 'review_parsing_links';

    protected $fillable = ['link', 'parsed', 'body', 'content', 'category_id'];

    protected array $cast = [
        'id' => 'integer',
        'category_id' => 'integer',
        'content' => 'json',
    ];
}