<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $table = 'article_comments';

    protected $fillable = ['comment', 'article_id', 'user_id', 'user_name', 'user_avatar', 'reply_id', 'status'];

    protected $casts = [
        'created_at'  => 'date:Y-m-d',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
