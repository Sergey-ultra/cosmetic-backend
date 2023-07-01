<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    public const TABLE = 'user_messages';

    protected $table = self::TABLE;

    protected $fillable = ['message', 'to_user', 'from_user', 'is_viewed'];

    protected $casts = [
        'to_user' => 'integer',
        'from_user' => 'integer',
        'is_viewed' => 'boolean'
    ];

}
