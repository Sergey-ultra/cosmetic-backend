<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property array  $content
 * @property int    $category_id
 * @property string $link
 */
class ReviewParsingLink extends Model
{
    public const UNPARSED = 0;
    public const PARSED = 1;
    public const PUBLISHED = 2;
    public const ARCHIVED = 3;

    public const STATUS_MAP = [
        self::UNPARSED => 'Не обработанная',
        self::PARSED => 'Обработанная',
        self::PUBLISHED => 'Опубликовано',
        self::ARCHIVED => 'В архиве',
    ];

    protected $table = 'review_parsing_links';

    protected $fillable = ['link', 'status', 'body', 'content', 'category_id'];

    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'content' => 'json',
    ];
}
