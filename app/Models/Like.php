<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public const TABLE = 'likes';

    protected $table = self::TABLE;

    protected $fillable = ['likeable_id', 'likeable_type', 'ip_address'];
}
