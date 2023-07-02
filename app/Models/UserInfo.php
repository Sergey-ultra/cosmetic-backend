<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    public const TABLE = 'user_infos';

    public const DEFAULT_AVATAR = '/storage/icons/user_avatar.png';
    public const TECHNICAL_SUPPORT_AVATAR = '/storage/icons/tech-support-avatar.svg';
    public const SEX_MAP = [
        'male' => 0,
        'female' => 1
    ];

    protected $table = self::TABLE;

    protected $fillable = ['user_id', 'avatar', 'sex', 'birthday_year'];
}
