<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public const TABLE = 'article_tag';

    protected $table = self::TABLE;

    /**
     * @var string[]
     */
    protected $fillable = ['tag', 'description', 'parent_id'];
}
