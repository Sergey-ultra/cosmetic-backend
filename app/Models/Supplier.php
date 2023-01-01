<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Supplier extends Model
{
    use HasApiTokens;

    protected $fillable = ['store_url', 'price_url', 'fio', 'email', 'phone'];
}
