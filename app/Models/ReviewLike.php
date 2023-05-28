<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewLike extends Model
{
    public const TABLE = 'review_likes';

    protected $table = self::TABLE;

    protected $fillable = ['review_id', 'plus_ip_address', 'minus_ip_address'];
}
