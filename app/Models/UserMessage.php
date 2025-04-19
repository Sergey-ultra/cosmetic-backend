<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMessage extends Model
{
    public const TABLE = 'user_messages';

    protected $table = self::TABLE;

    protected $fillable = ['message', 'chat', 'to_user', 'from_user', 'is_viewed', 'type', 'data'];

    protected $casts = [
        'to_user' => 'integer',
        'from_user' => 'integer',
        'is_viewed' => 'boolean',
        'data' => 'array',
        'created_at' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,  'from_user', 'id');
    }

}
