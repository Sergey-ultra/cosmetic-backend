<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    protected $fillable = ['article_id', 'ip_address'];
}
