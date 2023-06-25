<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMessage extends Model
{
    public const TABLE = 'client_messages';

    public const STATUS_PUBLISHED = 'published';

    protected $table = self::TABLE;

    protected $fillable = ['author', 'message', 'status'];
}
