<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['ip_address', 'user_id', 'user_name', 'sku_id', 'body', 'reply_id', 'status'];
}
