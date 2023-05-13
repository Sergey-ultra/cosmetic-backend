<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTelegramInfo extends Model
{
    public const TABLE = 'user_telegram_info';

    protected $table = self::TABLE;

    protected $fillable = ['hash', 'telegram_user_name', 'telegram_user_id', 'unsubscribe_code'];

    protected $casts = [
        'telegram_user_id' => 'integer',
        'unsubscribe_code' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
