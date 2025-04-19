<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    public const TABLE = 'article_views';

    protected $table = self::TABLE;

    protected $fillable = ['article_id', 'ip_address'];
}
