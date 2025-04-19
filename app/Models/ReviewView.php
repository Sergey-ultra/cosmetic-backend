<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewView extends Model
{
    public const TABLE = 'review_views';


    protected $table = self::TABLE;

    protected $fillable = ['review_id', 'ip_address', 'handled'];
}
