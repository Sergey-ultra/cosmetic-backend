<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    public const TABLE = 'user_infos';

    protected $table = self::TABLE;
    public const SEX_MAP = [
        'male' => 0,
        'female' => 1
    ];
    protected $fillable = ['user_id', 'avatar', 'sex', 'birthday_year'];
}
