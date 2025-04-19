<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParsingLink extends Model
{
    public const UNPARSED = 0;
    public const PARSED = 1;
    public const ABANDONED = 2;

    public const STATUS_MAP = [
        self::UNPARSED => 'Не обработанная',
        self::PARSED => 'Обработанная',
        self::ABANDONED => 'Отклоненная',
    ];
    protected $table = 'parsing_links';
    protected $fillable = ['link', 'parsed', 'store_id', 'category_id'];
}
