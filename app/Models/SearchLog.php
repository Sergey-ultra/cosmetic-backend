<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    public const TABLE = 'search_logs';

    protected $table = self::TABLE;

    protected $fillable = ['search_string'];
}
